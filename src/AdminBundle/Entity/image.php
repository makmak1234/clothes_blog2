<?php
// src/AppBundle/Bundle/Entity/image.php
namespace AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * BlogPost
 *
 * @ORM\Table(name="image_a")
 * @ORM\Entity(repositoryClass="AdminBundle\Repository\childrenGoodsRepository")
 * @ORM\HasLifecycleCallbacks
 */

class image
{

    //const SERVER_PATH_TO_IMAGE_FOLDER = __DIR__.'/../../../web/uploads/documents';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $path;

    /**
     * Unmapped property to handle file uploads
     */
    private $file;

    /**
    * @ORM\OneToMany(targetEntity="childrenGoodsColorNumber", mappedBy="image")
    */
    private $childrenGoodsColorNumber;

    /**
    * @ORM\OneToMany(targetEntity="childrenGoodsCategory", mappedBy="image")
    */
    private $childrenGoodsCategory;

    public function __construct()
    {
        $this->childrenGoodsColorNumber = new ArrayCollection();
        $this->childrenGoodsCategory = new ArrayCollection();
    }

    /*public function getBlogPosts()
    {
        return $this->blogPosts;
    }*/

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;

        if (isset($this->path)) {
            // store the old name to delete after the update
            $this->temp = $this->path;
            //$this->path = null;
        } 
        else {
            //$this->path = 'initial';
            $tm = time() . rand(10,100);
            //echo   "  FILE:  "  ;
            //print_r($this->file);
            $myfile_fit = $this->getMyFile();
            $myfile_ext = "." . $this->getMyFileExt();

            $string = 'April 15, 2003';
            $pattern = '[' . $myfile_ext . ']';
            $replacement = '';
            $myfile_fit = preg_replace($pattern, $replacement, $myfile_fit);

            $f_thum = $myfile_fit . $tm . $myfile_ext;//basename($myfile_fit) . $tm . $ext;
            $this->path = $f_thum;
        }
    }

    public function getMyFile()
    {
        //$myfile = new UploadedFile();
        //return $this->getFile()->getClientOriginalName();
        return $this->getFile()->getClientOriginalName();
    }

    public function getMyFileExt()
    {
        //$myfile = new UploadedFile();
        //return $this->getFile()->getClientOriginalName();
        return $this->getFile()->guessClientExtension();
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Manages the copying of the file to the relevant place on the server
     */
    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        // we use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and target filename as params
        $this->getFile()->move(
            //self::SERVER_PATH_TO_IMAGE_FOLDER,
            __DIR__.'/../../../web/uploads/documents',
            //$this->getFile()->getClientOriginalName()
            $this->path
        );

        // set the path property to the filename where you've saved the file
        $this->filename = $this->getFile()->getClientOriginalName();

        // clean up the file property as you won't need it anymore
        $this->setFile(null);
    }

    /**
     * Lifecycle callback to upload the file to the server
     */
    public function lifecycleFileUpload()
    {
        //$this->upload();
    }

    /**
     * Updates the hash value to force the preUpdate and postUpdate events to fire
     */
    public function refreshUpdated()
    {
        //$this->setUpdated(new \DateTime());
        $this->upload();
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return Image
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/documents';
    }


    // ... the rest of your class lives under here, including the generated fields
    //     such as filename and updated

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
     * Add childrenGoodsColorNumber
     *
     * @param \AdminBundle\Entity\childrenGoodsColorNumber $childrenGoodsColorNumber
     * @return Image
     */
    public function addChildrenGoodsColorNumber(\AdminBundle\Entity\childrenGoodsColorNumber $childrenGoodsColorNumber)
    {
        $this->childrenGoodsColorNumber[] = $childrenGoodsColorNumber;

        return $this;
    }

    /**
     * Remove childrenGoodsColorNumber
     *
     * @param \AdminBundle\Entity\childrenGoodsColorNumber $childrenGoodsColorNumber
     */
    public function removeChildrenGoodsColorNumber(\AdminBundle\Entity\childrenGoodsColorNumber $childrenGoodsColorNumber)
    {
        $this->childrenGoodsColorNumber->removeElement($childrenGoodsColorNumber);
    }

    /**
     * Get childrenGoodsColorNumber
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildrenGoodsColorNumber()
    {
        return $this->childrenGoodsColorNumber;
    }

    /**
     * Add childrenGoods
     *
     * @param \AdminBundle\Entity\childrenGoods $childrenGoods
     * @return image
     */
    public function addChildrenGood(\AdminBundle\Entity\childrenGoods $childrenGoods)
    {
        $this->childrenGoods[] = $childrenGoods;

        return $this;
    }

    /**
     * Remove childrenGoods
     *
     * @param \AdminBundle\Entity\childrenGoods $childrenGoods
     */
    public function removeChildrenGood(\AdminBundle\Entity\childrenGoods $childrenGoods)
    {
        $this->childrenGoods->removeElement($childrenGoods);
    }

    /**
     * Get childrenGoods
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildrenGoods()
    {
        return $this->childrenGoods;
    }

    /**
     * Add childrenGoodsCategory
     *
     * @param \AdminBundle\Entity\childrenGoods $childrenGoodsCategory
     * @return image
     */
    public function addChildrenGoodsCategory(\AdminBundle\Entity\childrenGoods $childrenGoodsCategory)
    {
        $this->childrenGoodsCategory[] = $childrenGoodsCategory;

        return $this;
    }

    /**
     * Remove childrenGoodsCategory
     *
     * @param \AdminBundle\Entity\childrenGoods $childrenGoodsCategory
     */
    public function removeChildrenGoodsCategory(\AdminBundle\Entity\childrenGoods $childrenGoodsCategory)
    {
        $this->childrenGoodsCategory->removeElement($childrenGoodsCategory);
    }

    /**
     * Get childrenGoodsCategory
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildrenGoodsCategory()
    {
        return $this->childrenGoodsCategory;
    }
}
