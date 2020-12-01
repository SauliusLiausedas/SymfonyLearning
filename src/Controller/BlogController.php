<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController
{

    public function __construct(SessionInterface $session, RouterInterface $router)
    {
        $this->session = $session;
        $this->router = $router;
    }

    /**
     * @Route("/", name="blog_index")
     */
    public function index()
    {
        return $this->render('blog/login.html.twig', [
            'posts' => $this->session->get('posts')
        ]);
    }

    /**
     * @Route("/add", name="blog_add")
     */
    public function add()
    {
        $posts = $this->session->get('posts');
        $posts[uniqid()] = [
            'title' => 'Random post '.rand(1, 500),
            'text' => 'Aliqua ea laboris consectetur culpa Lorem est ut sint Lorem nisi elit officia. Consequat do dolor qui id qui irure laborum mollit. Culpa dolore officia deserunt ad ipsum laboris. Adipisicing dolor anim id consequat deserunt. Deserunt excepteur labore cupidatat ipsum mollit. Et incididunt sint ea duis enim quis sit ullamco nostrud id eu. Commodo non veniam mollit deserunt laborum occaecat id laboris ex ipsum.'.rand(1, 500),
            'date' => new \DateTime(),
        ];

        $this->session->set('posts', $posts);

        return new RedirectResponse($this->router->generate('blog_index'));

    }

    /**
     * @Route("/show/{id}", name="blog_show")
     */
    public function show($id)
    {
        $posts = $this->session->get('posts');
        if (!$posts || !isset($posts[$id])) {
            throw new NotFoundHttpException('Post not found');
        }

        return $this->render('blog/post.html.twig', [
            'id' => $id,
            'post' => $posts[$id]
        ]);
    }
}