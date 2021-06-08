<?php

namespace App\Controller;

use App\Entity\AboutMe;
use App\Entity\Contact;
use App\Entity\Project;
use App\Form\ContactType;
use App\Repository\AboutMeRepository;
use App\Repository\ProjectRepository;
use App\Repository\TimelineRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    private $aboutMeRepository;
    private $timelineRepository;
    private $projectRepository;

    public function __construct(
        AboutMeRepository $aboutMeRepository,
        TimelineRepository $timelineRepository,
        ProjectRepository $projectRepository
    ) {
        $this->aboutMeRepository = $aboutMeRepository;
        $this->timelineRepository = $timelineRepository;
        $this->projectRepository = $projectRepository;
    }

    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        return $this->render('front/home.html.twig', [
            'aboutMe' => $this->aboutMeRepository->findAll()[0],
        ]);
    }

    /**
     * @Route("/a-propos", name="about_me")
     */
    public function aboutMe(): Response
    {
        return $this->render('front/timeline.html.twig', [
            'timeline' => $this->timelineRepository->findAll(),
        ]);
    }

    /**
     * @Route("/projets", name="projects")
     */
    public function projects(): Response
    {
        return $this->render('front/projects.html.twig', [
            'projects' => $this->projectRepository->findAll(),
        ]);
    }

    /**
     * @Route("/projet/{slug}", name="show_projet")
     */
    public function showProject(Project $project): Response
    {
        return $this->render('front/project.html.twig', [
            'project' => $project,
        ]);
    }

    /**
     * @Route("/contactez-moi", name="contact")
     */
    public function contact(Request $request): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($contact);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('front/contact.html.twig', [
            'contact' => $contact,
            'form' => $form->createView()
        ]);
    }
}
