<?php
/**
 */
namespace App\Gelsin\Repositories;



use App\Gelsin\Models\User;
use Illuminate\Http\Request;

interface UsersRepositoryInterface
{

    /**
     * @param $id
     * @return bool
     */
    public function getById($id);

    /**
     * @param $username
     * @return mixed
     */
    public function getByUsername($username);

    /**
     * @param Request $request
     * @return mixed
     */
    public function getAll(Request $request);

    /**
     * @param Request $request
     * @return mixed
     */
    public function create(Request $request);

    /**
     * @param $username
     * @param $activationCode
     * @return bool
     */
    public function activate($username, $activationCode);

    /**
     * @param Request $request
     * @return mixed
     */
    public function updateSettings(Request $request);

    /**
     * @param Request $request
     * @return mixed
     */
    public function updatePassword(Request $request);
}
