<?php

namespace Wcms;

use JamesMoss\Flywheel\Document;

class Modelauthtoken extends Modeldb
{

    const AUTHTOKEN_REPO_NAME = 'authtoken';


    public function __construct()
    {
        parent::__construct();
        $this->storeinit(self::AUTHTOKEN_REPO_NAME);
    }

    /**
     * Add a Token in the database according to the Users datas
     * 
     * @param User $user
     */
    public function add(User $user)
    {
        $datas = [
            'user' => $user->id(),
            'ip' => $_SERVER['SERVER_ADDR'],
            'creationdate' => '1'
        ];
        $tokendata = new Document($datas);
        return $this->repo->store($tokendata);

    }

    public function getbytoken(string $token)
    {
        return $this->repo->findById($token);
    }

    public function delete(string $token)
    {
        return $this->repo->delete($token);
    }

}






?>