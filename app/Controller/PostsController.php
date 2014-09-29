<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php 
    class PostsController extends AppController {
        public $helpers = Array('Html', 'From', 'Session');
//        public $components = Array('Session');
        

        public function index() {
            $this->set('posts', $this->Post->find('all'));
            
//            pr($this->Session->read('User.name');
//            $session->read('example')
        }
        
        public function view($id = null) {
            if (!$id) {
                throw new NotFoundException(__('Invalid post'));
            }

            $post = $this->Post->findById($id);
            if (!$post) {
                throw new NotFoundException(__('Invalid post'));
            }
            $this->set('post', $post);
        }
        
//        public function add() {
//            if ($this->request->is('post')) {
//                $this->Post->create();
//                if ($this->Post->save($this->request->data)) {
//                    $this->Session->setFlash(__('Your post has been saved.'));
//                    $this->redirect(array('action' => 'index'));
//                } else {
//                    $this->Session->setFlash(__('Unable to add your post.'));
//                }
//            }
//        }
        
        public function add() {
            if ($this->request->is('post')) {
                $this->request->data['Post']['user_id'] = $this->Auth->user('id');
                if ($this->Post->save($this->request->data)) {
                    $this->Session->setFlash(__('Your post has been saved.'));
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(__('Unable to add your post.'));
                }
            }
        }
        
        public function edit($id = null) {
            if (!$id) {
                throw new NotFoundException(__('Invalid post'));
            }

            $post = $this->Post->findById($id);
            if (!$post) {
                throw new NotFoundException(__('Invalid post'));
            }

            if ($this->request->is('post') || $this->request->is('put')) {
                $this->Post->id = $id;
                if ($this->Post->save($this->request->data)) {
                    $this->Session->setFlash(__('Your post has been updated.'));
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(__('Unable to update your post.'));
                }
            }

            if (!$this->request->data) {
                $this->request->data = $post;
            }
        }
        
        public function delete($id) {
            if ($this->request->is('get')) {
                throw new MethodNotAllowedException();
            }

            if ($this->Post->delete($id)) {
                $this->Session->setFlash(__('The post with id: %s has been deleted.', h($id)));
                $this->redirect(array('action' => 'index'));
            }
        }
        
        public function isAuthorized($user) {
            // All registered users can add posts
            if ($this->action === 'add') {
                return true;
            }

            // The owner of a post can edit and delete it
            if (in_array($this->action, array('edit', 'delete'))) {
                $postId = $this->request->params['pass'][0];
                if ($this->Post->isOwnedBy($postId, $user['id'])) {
                    return true;
                }
            }

            return parent::isAuthorized($user);
        }
    }
?>