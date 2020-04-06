<?php
namespace RoiUp\Zoom\Http\Api\Requests;

use RoiUp\Zoom\Http\Api\Request;
use RoiUp\Zoom\Models\Zoom\User as Model;
use RoiUp\Zoom\Models\Zoom\Panelist;
use RoiUp\Zoom\Models\Zoom\Registrant;

/**
 * Class Users
 * @package RoiUp\Zoom\Models\Zoom
 *
 */
class User extends Request
{

    /**
     * Create
     *
     * @param array $data
     * @return array|mixed
     */
    public function create(Model $user, $action = 'create')
    {
        $request = [
            'action' => $action,
            'user_info' => [
                "email" => $user->email, //required
                "type" => $user->type != '' ? $user->type : '1', // required
                "first_name" => $user->first_name, // optional
                "last_name" => $user->last_name, // optional
                "password" => $user->password // optional
            ],
        ];
        return $this->post('users', $request);
    }

    /**
     * Get Users
     *
     * @return array
     */
    public function getList()
    {
        $response = $this->get('users');
        $users = [];
        foreach($response['users'] as $user){
            $users[] = Model::instantiate($user);
        }
        return $users;
    }

    /**
     * Retrieve
     *
     * @param string $userId
     * @param array $optional
     * @return array|mixed
     */
    public function retrieve($userId)
    {
        return Model::instantiate($this->get("users/{$userId}"));
    }

    /**
     * Update
     *
     * @param string $userId
     * @param array $data
     * @return array|mixed
     */
    public function update($userId, Model $user)
    {
        $allowed_fields = ["type", "first_name", "last_name", "pmi", "timezone", "dept", "vanity_name", "host_key"];
        $request = [];
        foreach($allowed_fields as $field){
            if($user->$field != ''){
                $request[$field] = $user->$field;
            }
        }
        return $this->patch("users/{$userId}", $request);
    }

    /**
     * Update
     *
     * @param string $userId
     * @param string $password
     * @return array|mixed
     */
    public function updatePassword($userId, $password)
    {
        return $this->put("users/{$userId}/password", ['password' => $password]);
    }
    /**
     * Delete
     *
     * @param string $userId
     * @return array|mixed
     */
    public function delete($userId)
    {
        return $this->delete("users/{$userId}");
    }



    /**
     * Users Assistants List
     *
     * @param string $userId
     * @return array|mixed
     */
    public function assistantsList(string $userId)
    {
        return $this->get("users/{$userId}/assistants");
    }

    /**
     * Users Assistants List
     *
     * @param string $userId
     * @return array|mixed
     */
    public function emailExists(string $email)
    {
        return $this->get("users/email=email=". $email);
    }

    public function switchAccount(string $accountId, string $userId)
    {
        return $this->patch("accounts/{$accountId}/users/{$userId}/account", []);
    }

    // /**
    //  * Add Assistant
    //  *
    //  * @param string $userId
    //  * @param array $data
    //  * @return array|mixed
    //  */
    // public function addAssistant(string $userId, array $data)
    // {
    //     return $this->post("users{$userId}/assistants", $data);
    // }

    // /**
    //  * Delete Assistants
    //  *
    //  * @param string $userId
    //  * @return array|mixed
    //  */
    // public function deleteAssistants(string $userId)
    // {
    //     return $this->delete("users/{$userId}/assistants");
    // }

    // /**
    //  * Delete Assistant
    //  *
    //  * @param string $userId
    //  * @param string $assistantId
    //  * @return array|mixed
    //  */
    // public function deleteAssistant(string $userId, string $assistantId)
    // {
    //     return $this->delete("users/{$userId}/assistants/{$assistantId}");
    // }

    // /**
    //  * Schedulers List
    //  *
    //  * @param string $userId
    //  * @return array|mixed
    //  */
    // public function schedulersList(string $userId)
    // {
    //     return $this->get("users/{$userId}/schedulers");
    // }

    // /**
    //  * Deletes Schedulers
    //  *
    //  * @param string $userId
    //  * @return array|mixed
    //  */
    // public function deletesSchedulers(string $userId)
    // {
    //     return $this->delete("users/{$userId}/schedulers");
    // }

    // /**
    //  * Deletes Scheduler
    //  *
    //  * @param string $userId
    //  * @param string $schedulerId
    //  * @return array|mixed
    //  */
    // public function deletesScheduler(string $userId, string $schedulerId)
    // {
    //     return $this->delete("users/{$userId}/schedulers/{$schedulerId}");
    // }

}