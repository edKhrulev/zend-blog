<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Blog\Controller;

use Blog\Form\CommentForm;
use Blog\Model\Comment;
use Blog\Model\CommentTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class BlogController extends AbstractActionController
{
    protected $table;

    public function __construct(CommentTable $table)
    {
        $this->table = $table;
    }

    public function indexAction()
    {
        $paginator = $this->table->fetchAll(true);

        // Set the current page to what has been passed in query string,
        // or to 1 if none is set, or the page is invalid:
        $page = (int) $this->params()->fromQuery('page', 1);
        $page = ($page < 1) ? 1 : $page;
        $paginator->setCurrentPageNumber($page);

        // Set the number of items per page to 10:
        $paginator->setItemCountPerPage(10);

        return new ViewModel([
            'paginator' => $paginator
        ]);
    }

    public function addAction()
    {
//        $id = (int) $this->params()->fromRoute('id', 0);
//        if (!$id) {
//            return $this->redirect()->toRoute('home');
//        }

        $form = new CommentForm();
        $form->get('submit')->setValue('Save');

        $viewModel = new ViewModel(['form' => $form]);

        $request = $this->getRequest();

        if (!$request->isPost()) {
            return  $viewModel;
        }

        $comment = new Comment();
        $form->setInputFilter($comment->getInputFilter());
        $form->setData($request->getPost());

        if (!$form->isValid()) {
            return $viewModel;
        }

        $comment->exchangeArray($form->getData());
        $this->table->saveComment($comment);
        return $this->redirect()->toRoute('home');
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('home', ['action' => 'add']);
        }

        // Retrieve the album with the specified id. Doing so raises
        // an exception if the album is not found, which should result
        // in redirecting to the landing page.
        try {
            $comment = $this->table->getComment($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('home', ['action' => 'index']);
        }

        $form = new CommentForm();
        $form->bind($comment);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

        if (!$request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($comment->getInputFilter());
        $form->setData($request->getPost());

        if (!$form->isValid()) {
            return $viewData;
        }

        $this->table->saveComment($comment);

        // Redirect to album list
        return $this->redirect()->toRoute('home', ['action' => 'index']);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('home');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->table->deleteComment($id);
            }

            // Redirect to list of albums
            return $this->redirect()->toRoute('home');
        }

        return [
            'id'    => $id,
            'comment' => $this->table->getComment($id),
        ];
    }
}
