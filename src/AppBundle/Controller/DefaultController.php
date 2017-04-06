<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\Type\TaskType;

/**
 * @Security("has_role('ROLE_USER')")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function listAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        $repo = $em->getRepository('AppBundle:Task');
        $tasks = $repo->findByOwner($this->getUser());
        
        $task = new \AppBundle\Entity\Task();
        $task->setOwner($this->getUser());
        
        $form = $this->createForm(TaskType::class, $task)
                ->add('save', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($task);
            $em->flush();
            return $this->redirectToRoute('homepage');
        }
        
        return $this->render('default/index.html.twig', array(
            'tasks' => $tasks,
            'form' => $form->createView(),
        ));
    }
}
