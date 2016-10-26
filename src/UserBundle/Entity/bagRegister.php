<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * bagRegister
 *
 * @ORM\Table(name="a_bag_register")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\bagRegisterRepository")
 */
class bagRegister
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=30, nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="tel", type="string", length=13)
     */
    private $tel;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=30, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=true)
     */
    private $comment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="prodDatetimeUpdate", type="datetime")
     */
    private $regDatetime;


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
     * Set name
     *
     * @param string $name
     * @return bagRegister
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return bagRegister
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set tel
     *
     * @param string $tel
     * @return bagRegister
     */
    public function setTel($tel)
    {
        $this->tel = $tel;

        return $this;
    }

    /**
     * Get tel
     *
     * @return string 
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return bagRegister
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return bagRegister
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set regDatetime
     *
     * @param \DateTime $regDatetime
     * @return bagRegister
     */
    public function setRegDatetime($regDatetime)
    {
        $this->regDatetime = $regDatetime;

        return $this;
    }

    /**
     * Get regDatetime
     *
     * @return \DateTime 
     */
    public function getRegDatetime()
    {
        return $this->regDatetime;
    }
}
