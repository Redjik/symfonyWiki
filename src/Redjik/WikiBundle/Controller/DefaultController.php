<?php

namespace Redjik\WikiBundle\Controller;


use Redjik\WikiBundle\Model\Pages;
use Redjik\WikiBundle\Model\PagesQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class DefaultController
 *
 * base CRUD class for wiki pages
 * @package Redjik\WikiBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * Landing page
     * @return Response
     */
    public function indexAction()
    {
        $pages = PagesQuery::create()->findByParent(null);
        return $this->render('RedjikWikiBundle:Default:index.html.twig', ['pages' => $pages]);
    }

    /**
     * Shows page according alias
     * @param string $alias
     * @return Response
     */
    public function showAction($alias)
    {
        $page = $this->getPageFromAlias($alias);
        $response =$this->render('RedjikWikiBundle:Default:show.html.twig',['page'=>$page]);

        $response->setCache(array(
            'private'       => false,
            'public'        => true,
        ));

        return $response;
    }

    /**
     * Adds page according alias
     * @param string $alias
     * @return Response
     */
    public function addAction($alias)
    {
        $page = new Pages();
        $form = $this->createFormForPage($page);
        $form->handleRequest($this->getRequest());

        $parentPage = null;
        if ($alias!=='/'){
            $parentPage = $this->getPageFromAlias($alias);
        }

        if ($form->isValid() && $page->savePage($parentPage)){
            return $this->redirect($this->generateUrl('redjik_wiki_show',['alias'=>$page->getFullpath()]));
        }

        return $this->render('RedjikWikiBundle:Default:render.html.twig',['form'=>$form->createView()]);
    }

    /**
     * Edit page according alias
     * @param string $alias
     * @return Response
     */
    public function editAction($alias)
    {
        $page = $this->getPageFromAlias($alias);;
        $form = $this->createFormForPage($page);
        $form->handleRequest($this->getRequest());

        if ($form->isValid() && $page->savePage($page->getPagesRelatedByParent())){
            return $this->redirect($this->generateUrl('redjik_wiki_show',['alias'=>$page->getFullpath()]));
        }

        return $this->render('RedjikWikiBundle:Default:render.html.twig',['form'=>$form->createView()]);
    }

    /**
     * Delete page according alias
     * @param string $alias
     * @return Response
     */
    public function deleteAction($alias)
    {
        $page = $this->getPageFromAlias($alias);
        $form = $this->createFormBuilder()->add('submit','submit')->getForm();
        $form->handleRequest($this->getRequest());

        if ($form->isValid()){
            $page->delete();
            if ($page->isDeleted()){
                return $this->redirect($this->generateUrl('redjik_wiki_homepage'));
            }
        }

        return $this->render('RedjikWikiBundle:Default:delete.html.twig',['page'=>$page, 'form'=>$form->createView()]);
    }

    /**
     * @param $alias
     * @return Pages
     * @throws HttpException
     */
    protected function getPageFromAlias($alias)
    {
        $page = PagesQuery::create()->findOneByFullpath($alias);
        if (!$page){
            throw new HttpException(404,'Страница не найдена');
        }
        return $page;
    }

    /**
     * Generate form for wiki pages
     * @param Pages $page
     * @return \Symfony\Component\Form\Form
     */
    protected function createFormForPage(Pages $page)
    {
        return $this->createFormBuilder($page)
            ->add('title','text')
            ->add('alias','text')
            ->add('text','textarea')
            ->add('submit','submit')
            ->getForm();
    }

}
