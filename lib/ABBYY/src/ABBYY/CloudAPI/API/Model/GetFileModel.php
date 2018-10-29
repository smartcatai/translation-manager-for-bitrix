<?php

namespace Smartcat\ConnectorAPI\API\Model;

class GetFileModel
{
    /**
     * @var string
     */
    protected $id;
    /**
     * @var string
     */
    protected $token;
    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @param string $id
     *
     * @return self
     */
    public function setId($id = null)
    {
        $this->id = $id;
        return $this;
    }
    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
    /**
     * @param string $token
     *
     * @return self
     */
    public function setToken($token = null)
    {
        $this->token = $token;
        return $this;
    }
}