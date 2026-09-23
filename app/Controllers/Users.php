<?php
namespace App\Controllers;

use PHPCore\Core\Application\View;
use PHPCore\Core\System\Security;
use PHPCore\Core\System\Session;
use JetBrains\PhpStorm\NoReturn;
use PHPCore\Core\Http\Request;
use App\Models\UserModel;

/**
 *  Handles user management, including listing, creating, viewing, updating,
 *  and soft-deleting users.
 *
 * @author Julius Derigs <julius@derigs.top>
 */
class Users {
    /**
     * Displays an overview of all active users.
     *
     * @author Julius Derigs <julius@derigs.top>
     *
     * @return void
     */
    public function index(): void {
        $data = [
            'title' => esc(trans('users.titles.index')),
            'elements' => UserModel::all(['deleted' => 0])
        ];

        echo View::render('templates/header', $data) .
             View::render('users/index', $data) .
             View::render('templates/footer');
    }

    /**
     * Displays the user creation form and handles the creation of new users.
     *
     * @author Julius Derigs <julius@derigs.top>
     *
     * @return void
     */
    public function create(): void {
        $data = [
            'title' => esc(trans('users.titles.create'))
        ];

        $requiredFields = [
            'salutation',
            'first-name',
            'last-name',
            'email',
            'password'
        ];

        if (Request::method() === 'POST') {
            if (!Request::validate($requiredFields)) {
                Session::setFlashMessage('error', esc(trans('messages.error.formValidation')));

                redirect('create-user');
            }

            $input = [
                'salutation' => esc(Request::post('salutation')),
                'title' => esc(Request::post('title')),
                'first_name' => esc(Request::post('first-name')),
                'last_name' => esc(Request::post('last-name')),
                'email' => esc(Request::post('email')),
                'department' => esc(Request::post('department')),
                'password' => Security::hashPassword(Request::post('password')),
                'admin' => (int) Request::post('admin'),
                'active' => (int) Request::post('active')
            ];

            if (!$this->isEmailUnique($input['email'])) {
                Session::setFlashMessage('error', esc(trans('messages.error.emailUnique')));

                redirect('create-user');
            }

            if (UserModel::insert($input)) {
                Session::setFlashMessage('success', esc(trans('messages.success.save')));
            } else {
                Session::setFlashMessage('error', esc(trans('messages.error.save')));
            }

            redirect('users');
        }

        echo View::render('templates/header', $data) .
             View::render('users/create', $data) .
             View::render('templates/footer');
    }

    /**
     * Displays the details of a specific user.
     *
     * @author Julius Derigs <julius@derigs.top>
     *
     * @param int $id
     * @return void
     */
    public function show(int $id): void {
        $data = [
            'title' => esc(trans('users.titles.show')),
            'element' => UserModel::find($id)
        ];

        echo View::render('templates/header', $data) .
             View::render('users/show', $data) .
             View::render('templates/footer');
    }

    /**
     * Displays the user update form and handles updating an existing user.
     *
     * @author Julius Derigs <julius@derigs.top>
     *
     * @param int $id
     * @return void
     */
    public function update(int $id): void {
        $data = [
            'title' => esc(trans('users.titles.update')),
            'element' => UserModel::find($id)
        ];

        $requiredFields = [
            'salutation',
            'first-name',
            'last-name',
            'email'
        ];

        if (Request::method() === 'POST') {
            if (!Request::validate($requiredFields)) {
                Session::setFlashMessage('error', esc(trans('messages.error.formValidation')));

                redirect('update-user');
            }

            $input = [
                'salutation' => esc(Request::post('salutation')),
                'title' => esc(Request::post('title')),
                'first_name' => esc(Request::post('first-name')),
                'last_name' => esc(Request::post('last-name')),
                'email' => esc(Request::post('email')),
                'department' => esc(Request::post('department')),
                'admin' => (int) Request::post('admin'),
                'active' => (int) Request::post('active')
            ];

            if (!empty(Request::post('password'))) {
                $input['password'] = Security::hashPassword(Request::post('password'));
            }

            if (!$this->isEmailUnique($input['email'], $id)) {
                Session::setFlashMessage('error', esc(trans('messages.error.emailUnique')));

                redirect('update-user');
            }

            if (UserModel::update($id, $input)) {
                Session::setFlashMessage('success', esc(trans('messages.success.save')));
            } else {
                Session::setFlashMessage('error', esc(trans('messages.error.save')));
            }

            redirect('users');
        }

        echo View::render('templates/header', $data) .
             View::render('users/update', $data) .
             View::render('templates/footer');
    }

    /**
     * Soft-deletes a user and redirects to the user overview.
     *
     * @author Julius Derigs <julius@derigs.top>
     *
     * @param int $id
     * @return void
     */
    #[NoReturn]
    public function delete(int $id): void {
        if (UserModel::update($id, ['deleted' => 1])) {
            Session::setFlashMessage('success', esc(trans('messages.success.delete')));
        } else {
            Session::setFlashMessage('error', esc(trans('messages.error.delete')));
        }

        redirect('users');
    }

    /**
     * Checks whether an email address is unique among users.
     *
     * @author Julius Derigs <julius@derigs.top>
     *
     * @param string $email
     * @param int $id
     * @return bool
     */
    private function isEmailUnique(string $email, int $id = 0): bool {
        foreach (UserModel::all(['email' => $email]) as $element) {
            if ($element['id'] !== $id) {
                return false;
            }
        }

        return true;
    }
}