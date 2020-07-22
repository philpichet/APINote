<?php

namespace App\Controller;

use App\Entity\Grades;
use App\Entity\Students;
use App\Form\GradesType;
use App\Form\StudentsType;
use App\Repository\GradesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

/**
 * Class StudentsController
 * @package App\Controller
 * @Route("/api/students", name="api_students_")
 */
class StudentsController extends AbstractController
{

    /**
     * Return the list of student and the average of the class
     * @Route("", name="list", methods={"GET"})
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function list(EntityManagerInterface $em)
    {
        $average = $em->getRepository(Grades::class)->getAverageOfAll();
        $students = $em->getRepository(Students::class)->findAll();
        return $this->json(['average' => round($average, 2), "students" => $students], 200, [], ['groups' => ["students"], DateTimeNormalizer::FORMAT_KEY => "Y-m-d"]);
    }
    /**
     * Add a student
     * @Route("", name="add", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function add(Request $request, EntityManagerInterface $em)
    {
        $student = new Students();
        $form = $this->createForm(StudentsType::class, $student);
        $this->processForm($request, $form);
        if ($form->isValid()) {
            $em->persist($student);
            try {
                $em->flush();
                return $this->json($student, 201, [], ['groups' => 'student', DateTimeNormalizer::FORMAT_KEY => "Y-m-d"]);
            } catch (\Exception $e) {
                // The insertion failed, we return an error code and content
                return $this->json(["errors" => ['resource' => "Students has not been created"]], 503);
            }

        }
        // We return the array of errors
        return $this->json(["errors" => $this->processErrors($form)], 400);
    }

    /**
     * Return the student
     * @Route("/{id}", name="show", requirements={"id"="\d+"}, methods={"GET"})
     * @param Students|null $student
     * @param GradesRepository $repository
     * @return JsonResponse
     */
    public function show(?Students $student, GradesRepository $repository)
    {
        if (!$student instanceof Students)
            return $this->json(["errors" => ['resource' => 'Students not found']], 404);

        $student->average = round($repository->getAverageOfStudent($student), 2);
        return $this->json($student, 200, [], ['groups' => ["student","studentAverage"], DateTimeNormalizer::FORMAT_KEY => "Y-m-d"]);
    }


    /**
     * Update a student
     * @Route("/{id}", name="update", methods={"PUT"})
     * @param Students|null $student
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function update(?Students $student, Request $request, EntityManagerInterface $em)
    {
        if (!$student instanceof Students)
            return $this->json(["errors" => ['resource' => "Students not found"]], 404);
        $form = $this->createForm(StudentsType::class, $student);
        $this->processForm($request, $form);
        if ($form->isValid()) {
            try {
                $em->flush();
                return $this->json($student, 200, [], ['groups' => "student", DateTimeNormalizer::FORMAT_KEY => "Y-m-d"]);
            } catch (\Exception $e) {
                // The update failed, we return an error code and content
                return $this->json(["errors" => ['resource' => "Students has not been updated"]], 503);
            }
        }
        // We return the array of error
        return $this->json(["errors" => $this->processErrors($form)], 400);
    }

    /**
     * Add a grade of a student
     * @Route("/{id}/grades", requirements={"id"="\d+"}, methods={"POST"})
     * @param Students|null $student
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function addGrade(?Students $student, Request $request, EntityManagerInterface $em)
    {
        if (!$student instanceof Students)
            return $this->json(["errors" => ['resource' => "Students not found"]], 404);
        $grade = new Grades();
        // Attache the grade to the student
        $student->addGrade($grade);
        $form = $this->createForm(GradesType::class, $grade);
        $this->processForm($request, $form);
        if ($form->isValid()) {
            try {
                $em->persist($grade);
                $em->flush();
                return $this->json($grade, 201, [], ['groups' => ['newGrade', DateTimeNormalizer::FORMAT_KEY => "Y-m-d"]]);
            } catch (\Exception $ex) {
                return $this->json(["errors" => ["resource" => "Grades has not been insert"]], 503);
            }
        }
        return $this->json(["errors" => $this->processErrors($form)], 400);
    }

    /**
     * Delete a student
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @param Students|null $student
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function delete(?Students $student, Request $request, EntityManagerInterface $em)
    {
        if (!$student instanceof Students)
            return $this->json(["errors" => ['resource' => "Students not found"]], 404);

        $em->remove($student);
        try {
            $em->flush();
            return $this->json(null, 204);
        } catch (\Exception $e) {
            // The delete failed, we return an error code and content
            return $this->json(["errors" => ['resource' => "Students has not been deleted"]], 503);
        }

    }

    /**
     * Get the content of the request on an array and submit the form with it
     * @param Request $request
     * @param FormInterface $form
     */
    private function processForm(Request $request, FormInterface $form)
    {
        if($request->headers->has("content-type") && $request->headers->get("content-type") === "application/json") {
            $data = json_decode($request->getContent(), true);
        } else if ($request->headers->has("content-type") && $request->headers->get("content-type") === "application/x-www-form-urlencoded") {
            $data = $request->request->all();
        } else {
            throw new BadRequestHttpException("MIME Type can be application/x-www-form-urlencoded or application/json");
        }
        $form->submit($data);
    }

    /**
     * Store all errors in the form in an array
     * @param FormInterface $form
     * @return array
     */
    private function processErrors(FormInterface $form): array
    {
        $errors = [];
        if(!is_bool($form->getErrors()->getChildren()))
            // The form contains a global error, we had in the resource key
            $errors['resource'] = $form->getErrors()->getChildren()->getMessage();
        // Loop on each fiel for add all error if not valid
        foreach ($form->all() as $field) {
            if ($field->isValid())
                continue;
            $errors[$field->getName()] = $field->getErrors()->getChildren()->getMessage();
        }
        return $errors;
    }
}
