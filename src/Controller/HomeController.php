<?php
namespace App\Controller;

use App\Message\FormSubmissionMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class HomeController extends AbstractController
{
    private ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('Home/home.html.twig');
    }

    #[Route('/submit', name: 'form_submit', methods: ['POST'])]
    public function submit(Request $request, MessageBusInterface $bus): Response
    {
        $firstName = $request->request->get('firstName');
        $lastName = $request->request->get('lastName');
        $attachment = $request->files->get('attachment');

        if (empty($firstName) || empty($lastName)) {
            return new Response('First Name and Last Name are required!', Response::HTTP_BAD_REQUEST);
        }

        if ($attachment && !$attachment->isValid()) {
            return new Response('Invalid file uploaded.', Response::HTTP_BAD_REQUEST);
        }

        if ($attachment && $attachment->isValid()) {
            if ($attachment->getSize() > 2 * 1024 * 1024) {
                return new Response('File size must be less than 2MB.', Response::HTTP_BAD_REQUEST);
            }

            // Move the file to the upload directory
            $uploadDirectory = $this->params->get('upload_directory');
            $filePath = $attachment->move($uploadDirectory, $attachment->getClientOriginalName());
        }

        // Dispatch the message asynchronously
        $bus->dispatch(new FormSubmissionMessage($firstName, $lastName, $filePath?->getPathname()));

        return new Response('Form submitted successfully!');
    }
}