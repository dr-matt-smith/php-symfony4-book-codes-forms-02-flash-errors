<?php

namespace App\Controller;

use App\Entity\Student;
use App\Repository\StudentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class StudentController extends AbstractController
{
    /**
     * @Route("/student/new", name="student_new_form")
     */
    public function newForm()
    {
        $argsArray = [ ];
        $templateName = 'student/new';
        return $this->render($templateName . '.html.twig', $argsArray);
    }

    /**
     * @Route("/student/processNewForm", name="student_process_new_form")
     */
    public function processNewForm(Request $request)
    {
        // extract name values from POST data
        $firstName = $request->request->get('firstName');
        $surname = $request->request->get('surname');

        // valid if neither value is EMPTY
        $isValid = !empty($firstName) && !empty($surname);

        if(!$isValid){
            $this->addFlash(
        'error',
        'student firstName/surname cannot be an empty string'
            );

            // forward this to the createAction() method
            return $this->newForm($request);
        }

        // forward this to the createAction() method
        return $this->createAction($firstName, $surname);
    }


    /**
     * @Route("/student", name="student_list")
     */
    public function listAction()
    {
        $studentRepository = $this->getDoctrine()->getRepository('App:Student');
        $students = $studentRepository->findAll();

        $template = 'student/list.html.twig';
        $args = [
            'students' => $students
        ];
        return $this->render($template, $args);
    }

    /**
     * @Route("/student/create/{firstName}/{surname}", name="student_create")
     */
    public function createAction($firstName, $surname)
    {
        $student = new Student();
        $student->setFirstName($firstName);
        $student->setSurname($surname);

        // entity manager
        $em = $this->getDoctrine()->getManager();

        // tells Doctrine you want to (eventually) save the Product (no queries yet)
        $em->persist($student);

        // actually executes the queries (i.e. the INSERT query)
        $em->flush();

        return new Response('Created new student with id '.$student->getId());
    }



    /**
     * @Route("/student/{id}", name="student_show")
     */
    public function showAction(Student $student)
    {

        $template = 'student/show.html.twig';
        $args = [
            'student' => $student
        ];

        if (!$student) {
            $template = 'error/404.html.twig';
        }

        return $this->render($template, $args);
    }

    /**
     * @Route("/student/delete/{id}")
     */
    public function deleteAction(Student $student)
    {
        // store ID so we can still refer to it after object/row deleted
        $id = $student->getId();

        $em = $this->getDoctrine()->getManager();

        // tells Doctrine you want to (eventually) delete the Student (no queries yet)
        $em->remove($student);

        // actually executes the queries (i.e. the DELETE query)
        $em->flush();

        return new Response('Deleted student with id = '.$id);
    }

    /**
     * @Route("/student/update/{id}/{newFirstName}/{newSurname}")
     */
    public function updateAction(Student $student, $newFirstName, $newSurname)
    {
        $em = $this->getDoctrine()->getManager();

        $student->setFirstName($newFirstName);
        $student->setSurname($newSurname);
        $em->flush();

        return $this->redirectToRoute('student_show', [
            'id' => $student->getId()
        ]);

//        return $this->redirectToRoute('homepage');
    }
}
