<?php

namespace App\Controller;

use App\Entity\MicroPost;
use App\Form\MicroPostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MicroPostRepository;
use Symfony\Component\Routing\RouterInterface;

/**
 * @Route("/micro-post")
 */
class MicroPostController extends AbstractController
{

    /**
     * @var MicroPostRepository $microPostRepository
     */
    private $microPostRepository;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * MicroPostController constructor.
     * @param MicroPostRepository $microPostRepository
     * @param FormFactoryInterface $formFactory
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface $router
     */
    public function __construct(MicroPostRepository $microPostRepository,
                                FormFactoryInterface $formFactory,
                                EntityManagerInterface $entityManager,
                                RouterInterface $router
    )
    {
        $this->microPostRepository = $microPostRepository;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    /**
     * @Route("/", name="micro_post_index")
     */
    public function index(): Response
    {
        return $this->render('micro_post/index.html.twig', [
            'posts' => $this->microPostRepository->findBy([], ['time' => 'desc'])
        ]);
    }

    /**
     * @Route("/edit/{id}", name="micro_post_edit")
     * @param MicroPost $microPost
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit(MicroPost $microPost, Request $request)
    {

        $form = $this->formFactory->create(MicroPostType::class, $microPost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //  No persist needed
            $this->entityManager->flush();

            return new RedirectResponse($this->router->generate('micro_post_post', [
                'id' => $microPost->getId()
            ]));
        }

        return $this->render('micro_post/add.html.twig', [
            'postForm' => $form->createView()
        ]);

    }

    /**
     * @Route("/delete/{id}", name="micro_post_delete")
     * @param MicroPost $microPost
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(MicroPost $microPost, Request $request)
    {
        $this->entityManager->remove($microPost);
        $this->entityManager->flush();

        $request->getSession()->getFlashBag();
        $this->addFlash('notice', 'Post deleted');

        return new RedirectResponse($this->router->generate('micro_post_index'));
    }

    /**
     * @Route("/add", name="micro_post_add")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function add(Request $request) {

        $microPost = new MicroPost();
        $microPost->setTime(new \DateTime());

        $form = $this->formFactory->create(MicroPostType::class, $microPost);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($microPost);
            $this->entityManager->flush();

            return new RedirectResponse($this->router->generate('micro_post_index'));

        }

        return $this->render('micro_post/add.html.twig', [
            'postForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="micro_post_post")
     * @param MicroPost $post
     * @return Response
     */
    public function post(MicroPost $post) {

        return $this->render('micro_post/post.html.twig', [
            'post' => $post
        ]);

    }


}