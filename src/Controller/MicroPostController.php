<?php

namespace App\Controller;

use App\Entity\MicroPost as MicroPostAlias;
use App\Entity\User;
use App\Form\MicroPostType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
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
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(UserRepository $userRepository): Response
    {
        $currentUser = $this->getUser();

        $usersToFollow = [];

        if ($currentUser instanceof User) {
            $currentUser->getFollowing();
            $posts = $this->microPostRepository->findAllByUsers($currentUser->getFollowing());

            $usersToFollow = count($posts) === 0 ? $userRepository->findAllWithMoreThan5PostsExceptUser($currentUser) : [];
        } else {
            $posts = $this->microPostRepository->findBy(
                [],
                ['time' => 'desc']
            );
        }

        return $this->render('micro_post/index.html.twig', [
            'posts' => $posts,
            'usersToFollow' => $usersToFollow
        ]);
    }

    /**
     * @Route("/edit/{id}", name="micro_post_edit")
     * @param MicroPostAlias $microPost
     * @param Request $request
     * @return RedirectResponse|Response
     * @Security("is_granted('edit', microPost)", message="You don't have persmission to edit this post.")
     */
    public function edit(MicroPostAlias $microPost, Request $request)
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
     * @param MicroPostAlias $microPost
     * @param Request $request
     * @return RedirectResponse
     * @Security("is_granted('delete', microPost)", message="You don't have persmission to edit this post.")
     */
    public function delete(MicroPostAlias $microPost, Request $request)
    {
//        if (!$this->denyAccessUnlessGranted('delete', $microPost)) {
//            throw new UnauthorizedHttpException();
//        }

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
    public function add(Request $request)
    {
        $user = $this->getUser();
        $microPost = new MicroPostAlias();
        /** @noinspection PhpParamsInspection */
        $microPost->setUser($user);

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
     * @Route("/user/{username}", name="micro_post_user")
     * @param User $userWithPosts
     * @return Response
     */
    public function userPosts(User $userWithPosts)
    {
        return $this->render('micro_post/user-posts.html.twig', [
//            'posts' => $userWithPosts->getMicroPosts()
            'posts' => $this->microPostRepository->findBy(
                ['user' => $userWithPosts],
                ['time' => 'desc']
            ),
            'user' => $userWithPosts
        ]);
    }

    /**
     * @Route("/{id}", name="micro_post_post")
     * @param MicroPostAlias $post
     * @return Response
     */
    public function post(MicroPostAlias $post)
    {
        return $this->render('micro_post/post.html.twig', [
            'post' => $post
        ]);
    }


}