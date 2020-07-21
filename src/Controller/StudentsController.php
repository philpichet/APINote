<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class StudentsController
 * @package App\Controller
 * @Route("/api/students", name="api_studentd_")
 */
class StudentsController extends AbstractController
{
    /**
     * Add a student
     * @Route("", name="add", methods={"POST"})
     */
    public function add(Request $request, EntityManagerInterface $em)
    {
        $student = new Student();
        $form = $this->createForm(StudentType::class, $student);
        $this->processForm($request, $form);
        if($form->isValid()) {
            $em->persist($student);
            try {
                $em->flush();
                return $this->json( $student, 201, ['Content-Type' => "application/json"]);
            } catch (\Exception $e) {
                // The insertion failed, we return an error code and content
                return $this->json(["errors" => ['resource' => "Student can not be created"]], 400, ['Content-Type' => "application/json"]);
            }

        }
        // We return the array of errors
        return $this->json(["errors" => $this->processErrors($form)], 400, ['Content-Type' => "application/json"]);
    }

    /**
     * Update a student
     * @Route("/{id}", name="update", methods={"PUT"})
     * @param Student|null $student
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function update(?Student $student, Request $request, EntityManagerInterface $em)
    {
        if($student === null)
            return $this->json(["errors" => ['resource' => "Student not found"]], 404, ['Content-Type' => "application/json"]);
        $form = $this->createForm(StudentType::class, $student);
        $this->processForm($request, $form);
        if($form->isValid()) {
            $em->flush();
            try {
                $em->flush();
                return $this->json($student, 200, ['Content-Type' => "application/json"]);
            } catch (\Exception $e) {
                // The update failed, we return an error code and content
                return $this->json(["errors" => ['resource' => "Student can not be updated"]], 400, ['Content-Type' => "application/json"]);
            }
        }
        // We return the array of error
        return $this->json(["errors" => $this->processErrors($form)], 400, ['Content-Type' => "application/json"]);
    }


    /**
     * Delete a student
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Student|null $student
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function delete(?Student $student, Request $request, EntityManagerInterface $em)
    {
        if($student === null)
            return $this->json(["errors" => ['resource' => "Student not found"]], 404, ['Content-Type' => "application/json"]);

        $em->remove($student);
        try {
            $em->flush();
            return $this->json(null, 204, ['Content-Type' => "application/json"]);
        } catch (\Exception $e) {
            // The delete failed, we return an error code and content
            return $this->json(["errors" => ['resource' => "Student can not be deleted"]], 400, ['Content-Type' => "application/json"]);
        }

    }

    /**
     * Get the content of the request on an array and submit the form with it
     * @param Request $request
     * @param FormInterface $form
     */
    private function processForm(Request $request, FormInterface $form)
    {
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
    }

    /**
     * Store all errors in the form in an array
     * @param FormInterface $form
     * @return array
     */
    private function processErrors(FormInterface $form) : array
    {
        $errors = [];
        foreach ($form->all() as $field) {
            if($field->isValid())
                continue;
            $errors[$field->getName()] = $field->getErrors()->getChildren()->getMessage();
        }
        return $errors;
    }
}
