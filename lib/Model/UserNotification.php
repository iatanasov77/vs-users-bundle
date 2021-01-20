<?php namespace VS\UsersBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use IA\PaymentBundle\Entity\PaymentDetails;

class UserNotification
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="VS\UsersBundle\Entity\User", inversedBy="notifications")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     */
    private $user;

    /**
     * @var type
     *
     * @ORM\Column(name="notification", type="string", length=255, nullable=false)
     */
    private $notification;

    /**
     *
     * @var type 
     * 
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    function getId()
    {
        return $this->id;
    }

    function getUser()
    {
        return $this->user;
    }

    function getDate()
    {
        return $this->date;
    }

    function setId($id)
    {
        $this->id = $id;
        
        return $this;
    }

    function setUser($user)
    {
        $this->user = $user;
        
        return $this;
    }

    function setDate($date)
    {
        $this->date = $date;
        
        return $this;
    }
    
    function setNotification($notification)
    {
        $this->notification = $notification;
        
        return $this;
    }

    function getNotification()
    {
        return $this->notification;
    }
}
