<?php

namespace CoreBundle\Entity;

/**
 * UserRelation
 */
class UserRelation
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \CoreBundle\Entity\User
     */
    private $parent;

    /**
     * @var \CoreBundle\Entity\User
     */
    private $child;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set parent
     *
     * @param \CoreBundle\Entity\User $parent
     *
     * @return UserRelation
     */
    public function setParent(\CoreBundle\Entity\User $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \CoreBundle\Entity\User
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set child
     *
     * @param \CoreBundle\Entity\User $child
     *
     * @return UserRelation
     */
    public function setChild(\CoreBundle\Entity\User $child = null)
    {
        $this->child = $child;

        return $this;
    }

    /**
     * Get child
     *
     * @return \CoreBundle\Entity\User
     */
    public function getChild()
    {
        return $this->child;
    }
}

