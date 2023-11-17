<?php

namespace App\Controller;

use App\Entity\Contact;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;


class ContactController extends AbstractController
{
    #[Route('/', name: 'app_contact')]
    public function index(ManagerRegistry $orm): Response
    {
        $contacts = $orm->getRepository(Contact::class)->findAll();
        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController', 'contacts' => $contacts
        ]);
    }

    #[Route('/contact/info/{id}', name: 'app_contact_info')]
    public function info_contact(ManagerRegistry $orm, $id): Response
    {
        $contact = $orm->getRepository(Contact::class)->find($id);
        return $this->render('contact/info.html.twig', [
            'controller_name' => 'ContactController', 'contact' => $contact
        ]);
    }

    #[Route('/contact/create', name: 'app_contact_create')]
    public function create(Request $request,EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $entityManager->persist($formData);
    
            $entityManager->flush();
            return $this->redirectToRoute('app_contact');
            }
        return $this->render('contact/create.html.twig', [
            'controller_name' => 'ContactController', 'form' => $form->createView()
        ]);
    }
    #[Route('/contact/edit/{id}', name: 'app_contact_edit')]
    public function edit(Request $request,EntityManagerInterface $entityManager, $id, ManagerRegistry $orm): Response
    {
        $contact = $orm->getRepository(Contact::class)->find($id);
        if (!$contact) {
            throw $this->createNotFoundException('Contact non trouvée');
        }
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($contact);
    
            $entityManager->flush();
            return $this->redirectToRoute('app_contact');
            }
        return $this->render('contact/edit.html.twig', [
            'form' => $form->createView()
        ]);
        
    }
    #[Route('/contact/delete/{id}', name: 'app_contact_delete')]
    public function delete(ManagerRegistry $orm, $id, EntityManagerInterface $entityManager): Response
    {
        $contact = $orm->getRepository(Contact::class)->find($id);
        if (!$contact) {
            throw $this->createNotFoundException('Contact non trouvée');
        }
        $entityManager->remove($contact);
        $entityManager->flush();
        return $this->redirectToRoute('app_contact');
    }
}
