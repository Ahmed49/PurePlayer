<?php

namespace MediaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use MediaBundle\Entity\Media;
use MediaBundle\Form\MediaType;

/**
 * Media controller.
 *
 * @Route("/admin/media")
 */
class MediaController extends Controller
{
    /**
     * Lists all Media entities.
     *
     * @Route("/", name="media_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $media = $em->getRepository('MediaBundle:Media')->findAll();
        $user = $this->getUser();
        return $this->render('MediaBundle:admin:media/index.html.twig', array(
            'media' => $media,'current_user' => $user));
    }

    /**
     * Creates a new Media entity.
     *
     * @Route("/new", name="media_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $medium = new Media();
        $form = $this->createForm('MediaBundle\Form\MediaType', $medium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$media_content store the uploaded file
            $media_content = $medium->getFilePath();
            // genere un nom avec MD5
            $fileName =  md5(uniqid()).'.'.$media_content->guessExtension();
            // on stock le fichier
            $media_content->move($this->getParameter('media_files_directory'),
                $fileName
            );
            // on met le nom du fichier en BDD
            $medium->setFilePath($fileName);

            // $file stores the uploaded miniature File
            $file_miniature = $medium->getPath();
            // Generate a unique name for the file before saving it
            $MiniaturefileName = md5(uniqid()).'.'.$file_miniature->guessExtension();
            // Move the file to the directory where brochures are stored
            $file_miniature->move(
                $this->getParameter('medias_directory'),
                $MiniaturefileName
            );
            // store file name in bdd
            // instead of its contents
            $medium->setPath($MiniaturefileName);

            $em = $this->getDoctrine()->getManager();
            $em->persist($medium);
            $em->flush();

            return $this->redirectToRoute('media_show', array('id' => $medium->getId()));
        }

        return $this->render('MediaBundle:admin:media/new.html.twig', array(
            'medium' => $medium,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Media entity.
     *
     * @Route("/{id}", name="media_show")
     * @Method("GET")
     */
    public function showAction(Media $medium)
    {
        $deleteForm = $this->createDeleteForm($medium);

        return $this->render('MediaBundle:admin:media/show.html.twig', array(
            'medium' => $medium,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Media entity.
     *
     * @Route("/{id}/edit", name="media_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Media $medium)
    {
        $deleteForm = $this->createDeleteForm($medium);
        $editForm = $this->createForm('MediaBundle\Form\MediaType', $medium);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            //$media_content store the uploaded file
            $media_content = $medium->getFilePath();
            // genere un nom avec MD5
            $fileName =  md5(uniqid()).'.'.$media_content->guessExtension();
            // on stock le fichier
            $media_content->move($this->getParameter('media_files_directory'),
                $fileName
            );
            // on met le nom du fichier en BDD
            $medium->setFilePath($fileName);
            
            // $file stores the uploaded miniature File
            $file_miniature = $medium->getPath();
            // Generate a unique name for the file before saving it
            $MiniaturefileName = md5(uniqid()).'.'.$file_miniature->guessExtension();
            // Move the file to the directory where brochures are stored
            $file_miniature->move(
                $this->getParameter('medias_directory'),
                $MiniaturefileName
            );
            // store file name in bdd
            // instead of its contents
            $medium->setPath($MiniaturefileName);

            $em = $this->getDoctrine()->getManager();
            $em->persist($medium);
            $em->flush();

            return $this->redirectToRoute('media_edit', array('id' => $medium->getId()));
        }

        return $this->render('MediaBundle:admin:media/edit.html.twig', array(
            'medium' => $medium,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Media entity.
     *
     * @Route("/{id}", name="media_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Media $medium)
    {
        $form = $this->createDeleteForm($medium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($medium);
            $em->flush();
        }

        return $this->redirectToRoute('media_index');
    }

    /**
     * Creates a form to delete a Media entity.
     *
     * @param Media $medium The Media entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Media $medium)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('media_delete', array('id' => $medium->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
