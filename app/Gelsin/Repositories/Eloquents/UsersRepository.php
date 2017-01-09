<?php

namespace App\Gelsin\Repositories\Eloquents;



use App\Gelsin\Models\User;
use App\Gelsin\Repositories\UsersRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersRepository extends AbstractRepository implements UsersRepositoryInterface
{
    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->model = $user;

    }

    /**
     * @param $id
     * @return bool
     */
    public function getById($id)
    {
        $user = $this->model->whereId($id)->firstOrFail();

        return $user;
    }

    /**
     * @param $username
     * @return mixed
     */
    public function getByUsername($username)
    {
        $user = $this->model->whereUsername($username)->firstOrFail();

        return $user;
    }

    /**
     * @return mixed
     */
    public function getAll(Request $request)
    {
        return $this->users($request)->with('category')->orderBy('created_at', 'desc')->paginate(perPage());
    }



    /**
     * @param Request $request
     * @return bool
     */
    public function create(Request $request)
    {
        $activationCode = sha1(str_random(11) . (time() * rand(2, 2000)));

        $this->model->username = $request->get('username');
        $this->model->email = $request->get('email');
        $this->model->password = Hash::make($request->get('password'));
        $this->model->password = Hash::make($request->get('password'));

        $admin = $request->get('is_admin');

        if (isset($admin)) {
            $this->model->is_customer = 0;
        }

        $this->model->email_confirmation = $activationCode;
        $this->model->save();


        // -- Here we will send email or sms for confirmation (later)
//        $this->mailer->activation($this->model, $activationCode);

        return true;
    }


    /**
     * @param Request $request
     * @return mixed
     */
    public function updateSettings(Request $request)
    {
        $user = $this->model->whereId(auth()->user()->id)->first();


        return $user;
    }

    /**
     * @param $username
     * @param $activationCode
     * @return bool
     */
    public function activate($username, $activationCode)
    {
        $user = $this->model->whereUsername($username)->first();
        if ($user && $user->email_confirmation === $activationCode) {
            $user->confirmed_at = Carbon::now();
            $user->save();

            return $user;
        }

        return false;
    }

    /**
     * @param $username
     * @param null $i
     * @return string
     */
    private function getUniqueUserName($username, $i = null)
    {
        $username = str_slug($username . ' ' . $i);
        if (!$username) {
            $username = str_random(5);
        }
        if ($this->model->whereUsername($username)->count()) {
            return $this->getUniqueUserName($username, ++$i);
        }

        return $username;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function updatePassword(Request $request)
    {
        if (Hash::check($request->get('currentpassword'), $request->user()->password)) {
            $user = auth()->user();
            $user->password = bcrypt($request->get('password'));
            $user->save();

            return true;
        }

        return false;
    }

    private function users($request = null)
    {
        $user = $this->model->freelancer();
        if ($request->query('q')) {
            $user = $user->where('username', 'LIKE', '%' . $request->query('q') . '%')
                ->orWhere('tags', 'LIKE', '%' . $request->query('q') . '%')
                ->orWhere('about', 'LIKE', '%' . $request->query('q') . '%');
        }
        if ($request->query('category')) {
            if ($category = $this->category->whereSlug($request->query('category'))->first()) {
                $user = $user->whereCategoryId($category->id);
            }
        }
        if ($request->query('tag')) {
            $user = $user->where('tags', 'LIKE', '%' . $request->query('tag') . '%');
        }

        return $user;
    }

    public function sendContactEmail($request)
    {
        $description = parseDown($request->get('description'));
        $toUser = $this->model->whereUsername($request->route('username'))->firstOrFail();
        $this->mailer->contactEmail($toUser, auth()->user(), $description);
    }
}
