<?php

namespace UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use UserBundle\Entity\bagRegister;
use UserBundle\Entity\buyClients;
use AdminBundle\Entity\childrenGoods;
use AdminBundle\Entity\childrenGoodsCategory;
use AdminBundle\Entity\childrenGoodsSizeNumber;
use AdminBundle\Form\childrenGoodsType;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * indexUserController controller.
 * 
 */
class indexUserController extends Controller
{

    protected $myvar;

    /**
     * Lists all childrenGoods entities.
     *
     * @Route("/", name="index_user")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $childrenGoods = $em->getRepository('AdminBundle:childrenGoods')->findAll();
        $childrenGoodsCategory = $em->getRepository('AdminBundle:childrenGoodsCategory')->findAll();
        $sourcePath = array();

        $cacheManager = $this->container->get('liip_imagine.cache.manager');

        foreach($childrenGoodsCategory as $indCat => $goodCategory){
            $pathImg = '/uploads/documents/' . $goodCategory->getImage()->getPath();
            //print_r('pathImg: ' . $pathImg);
            //print_r('<br>');
            $sourcePath[] = $cacheManager->getBrowserPath($pathImg, 'my_thumb_category');
        }

        $response = $this->render('UserBundle::indexUser.html.twig', array(
            'childrenGoods' => $childrenGoods,
            'childrenGoodsCategory' => $childrenGoodsCategory,
            'sourcePath' => $sourcePath,
        ));

        $response->setEtag(md5($response->getContent()));
        $response->setPublic(); // make sure the response is public/cacheable
        $response->isNotModified($request);

        return $response;
    }

    public function smallBagAction(Request $request)
    {
        //$nidAll = $request->query->get('nidAll');
        $session = $request->getSession();

        $nidAll = 0;
        if ($session->get('nid')) {
            $nid = $session->get('nid');
            foreach ($nid as $key => $value) {
                $nidAll += $value;
            }
        }

        return $this->render('UserBundle::smallBag.html.twig', array('nidAll' => $nidAll));
    }

    public function smallBagAltAction($nidAll)
    {
        $small_error = error_log($kernel->getLog());
        return $this->render('UserBundle::smallBagAlt.html.twig', array('small_error' => $small_error));
    }

    /**
     * Creates a new childrenGoods entity.
     *
     * @Route("/bag_register", name="bag_register")
     * @Method({"GET", "POST"})
     */
    public function bagRegisterAction(Request $request)
    {
        //$session = new Session();
        $session = $request->getSession();

        $bagRegister = new bagRegister;

        $bagRegister->setRegDatetime(new \DateTime('now'));
        $token = "qwerty";

        if($session->get('bagRegister') != null){
            $bagRegisterSession = $session->get('bagRegister');
            $token = $session->get('token');
            $bagRegister->setName($bagRegisterSession->getName());
            $bagRegister->setCity($bagRegisterSession->getCity());
            $bagRegister->setTel($bagRegisterSession->getTel());
            $bagRegister->setComment($bagRegisterSession->getComment());
            
            //$session-> invalidate();
        }

        $form = $this->createForm('UserBundle\Form\bagRegisterType', $bagRegister);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if($_POST["back_shop"] == 'true'){
                $back_shop = $_POST["back_shop"];
                $session->set('bagRegister', $bagRegister);

                print "_token ";
                var_dump($_POST["bag_register"]["_token"]);

                $session->set('token', $_POST["bag_register"]["_token"]);
                
                return $this->redirectToRoute('index_user');
            }

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $query = $em->createQuery(//WHERE p.orderclient = max(p.orderclient)
                    'SELECT max(p.orderclient)
                    FROM UserBundle:bagRegister p
                    '
                );

                $orderclientmax = $query->getResult();//->getOrderclient();

                //print_r('orderclientmax: ');
                //print_r($orderclientmax[0][1]); print_r('<br>');

                //$bagRegister->setOrderclient($orderclientmax + 1);//select max(age) from person
                if($orderclientmax[0][1] < 283758){
                 $bagRegister->setOrderclient(283758);
                }
                else{
                $bagRegister->setOrderclient($orderclientmax[0][1] + 1);
                }

                $repository = $em->getRepository('AdminBundle:childrenGoods');

                $idarr = $session->get('idbasketsmall');
                $nid = $session->get('nid');
                $sizearr = $session->get('sizearr');
                $colorarr = $session->get('colorarr');
                $priceall = 0;

                foreach($idarr as $k=>$v){
                    $buyClients = new buyClients;
                    $childrenGoods = $repository->find($v);
                    $priceone[] = $childrenGoods->getPriceGoods()->getRub();
                    $pricegoods[] = $priceone[$k] * $nid[$k];
                    $priceall += $pricegoods[$k];

                    if ($sizearr[$k] != 'undefined') {
                        $sizeTitle[] = $childrenGoods->getChildrenGoodsSizeNumber()->get($sizearr[$k])->getSize()->getSize();
                    }
                    else{
                        $sizeTitle[] = '';
                    }
                    

                    if ($colorarr[$k] != 'undefined') {
                        $colorTitle[] = $childrenGoods->getChildrenGoodsSizeNumber()->get($sizearr[$k])->getChildrenGoodsColorNumber()->get($colorarr[$k])->getColor()->getColor();
                    }
                    else{
                        $colorTitle[] = '';
                    }

                    $valuta = '';//?????

                    $buyClients->setSize($sizeTitle[$k]);
                    $buyClients->setColor($colorTitle[$k]);
                    $buyClients->setNid($nid[$k]);
                    $buyClients->setPriceone($priceone[$k]);

                    $buyClients->setBagRegister($bagRegister);
                    $buyClients->setChildrenGoods($childrenGoods);

                    //$em->persist($bagRegister);
                    $em->persist($buyClients);

                    //отправка email
                    $name = $bagRegister->getName();
                    $order = $bagRegister->getOrderclient();
                    $tel = $bagRegister->getTel();
                    $email = $bagRegister->getEmail();
                    $city = $bagRegister->getCity();
                    $title[] = $childrenGoods->getTitle();
                        
                }

                $em->persist($bagRegister);
                $em->flush();

                $comment = $bagRegister->getComment();

                $message = \Swift_Message::newInstance()
                    ->setSubject('Покупки одежды')
                    ->setFrom('send@example.com')
                    ->setTo('qwertyfamiliya@gmail.com')
                    ->setBody(
                        $this->renderView(
                            'UserBundle::emailAdmin.html.twig',
                            array('name' => $name,
                                    'order' => $order,
                                    'tel' => $tel,
                                    'email' => $email,
                                    'city' => $city,
                                    'title' => $title,
                                    'sizeTitle' => $sizeTitle,
                                    'colorTitle' => $colorTitle,
                                    'nid' => $nid,
                                    'priceone' => $priceone,
                                    'pricegoods' => $pricegoods,
                                    'comment' => $comment,
                                    'priceall' => $priceall,
                                )
                        ),
                        'text/html'
                    );

                $this->get('mailer')->send($message);
                //end email 

                $session-> invalidate();

                return $this->render('UserBundle::thanks.html.twig', array(
                'name' => $name,
                'order' => $order,
                'tel' => $tel,
                'email' => $email,
                'city' => $city,
                'title' => $title,
                'sizeTitle' => $sizeTitle,
                'colorTitle' => $colorTitle,
                'nid' => $nid,
                'priceone' => $priceone,
                'pricegoods' => $pricegoods,
                'comment' => $comment,
                'priceall' => $priceall,
                ));
            }
        }

        if($request->query->get('id')){
            $id = $request->query->get('id');
        }
        else{
            $id = 0;
        }
        
        return $this->render('UserBundle::bagRegister.html.twig', array(
            'token' => $token,
            'form' => $form->createView(),
            'mytest' => "mycity",
            'id' => $id,
        ));
    }

    /**
     * Finds and displays a childrenGoods entity.
     *
     * @Route("/{children_goods_category_id}/{children_goods_subcategory_id}/{id}", name="user_show", requirements={"id": "\d+"})
     * @Method("GET")
     */
    public function showAction(childrenGoods $childrenGood, Request $request, $children_goods_category_id, $children_goods_subcategory_id)
    {
        $session = $request->getSession();

        $bigBagDisp = 'none';

        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository('AdminBundle:childrenGoodsCategory')
                    ->findOneById($children_goods_category_id);

        $subcategory = $em->getRepository('AdminBundle:childrenGoodsSubcategory')
                    ->findOneById($children_goods_subcategory_id);

        $cacheManager = $this->container->get('liip_imagine.cache.manager');

        foreach($childrenGood->getChildrenGoodsSizeNumber()  as $indSize => $size){
            foreach ($size->getChildrenGoodsColorNumber() as $indColor => $color) {
                $pathImg = '/uploads/documents/' . $color->getImage()->getPath();
                $sourcePath[$indSize][$indColor] = $cacheManager->getBrowserPath($pathImg, 'my_thumb_show');
            }

        }

        $response = $this->render('UserBundle::showGood.html.twig', array(
            'childrenGood' => $childrenGood,
            'sourcePath' => $sourcePath,
            'category' => $category,
            'subcategory' => $subcategory,
        ));

        $response->setEtag(md5($response->getContent()));
        $response->setPublic(); // make sure the response is public/cacheable
        $response->isNotModified($request);

        return $response;
    }

    /**
     * Finds and displays a childrenGoods entity.
     *
     * @Route("/{children_goods_category_id}/{children_goods_subcategory_id}", name="cat_sub_show", requirements={"children_goods_category_id": "\d+", "children_goods_subcategory_id": "\d+"})
     * @Method("GET")
     */
    public function showSubcatAction($children_goods_category_id, $children_goods_subcategory_id, Request $request)
    // , lastmodified="post.getUpdatedAt()", etag="'Post' ~ post.getId() ~ post.getUpdatedAt()"
    {

        $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository('AdminBundle:childrenGoods');

        $category = $em->getRepository('AdminBundle:childrenGoodsCategory')
                    ->findOneById($children_goods_category_id);

        $subcategory = $em->getRepository('AdminBundle:childrenGoodsSubcategory')
                    ->findOneById($children_goods_subcategory_id);

        $childrenGoods = $subcategory->getChildrenGoods();
        /*$query = $repository->createQueryBuilder('p')
            ->where('p.childrenGoodsCategory = :children_goods_category_id AND p.childrenGoodsSubcategory = :children_goods_subcategory_id')
            ->setParameter('children_goods_category_id', $category)
            ->setParameter('children_goods_subcategory_id', $subcategory)
            ->orderBy('p.title', 'ASC')
            ->getQuery();

        $childrenGoods = $query->getResult();*/

        $cacheManager = $this->container->get('liip_imagine.cache.manager');

        foreach($childrenGoods as $indCat => $childrenGood){ //
            $pathImg = '/uploads/documents/' . $childrenGood->getChildrenGoodsSizeNumber()->get(0)->getChildrenGoodsColorNumber()->get(0)->getImage()->getPath();
            $sourcePath[] = $cacheManager->getBrowserPath($pathImg, 'my_thumb_subcat');
        }

        $response = $this->render('UserBundle::showSubcat.html.twig', array(
            'childrenGoods' => $childrenGoods,
            'sourcePath' => $sourcePath,
            'category' => $category,
            'subcategory' => $subcategory,
            //'nidAll' => $nidAll,
           
        ));

        $response->setEtag(md5($response->getContent()));
        $response->setPublic(); // make sure the response is public/cacheable
        $response->isNotModified($request);

        return $response;
    }

    /**
     * Displays a form to edit an existing childrenGoods entity.
     *
     * @Route("/{id}/edit", name="childrengoods_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, childrenGoods $childrenGood)
    {
        $deleteForm = $this->createDeleteForm($childrenGood);
        $editForm = $this->createForm('AdminBundle\Form\childrenGoodsType', $childrenGood);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($childrenGood);
            $em->flush();

            return $this->redirectToRoute('childrengoods_edit', array('id' => $childrenGood->getId()));
        }

        return $this->render('childrengoods/edit.html.twig', array(
            'childrenGood' => $childrenGood,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a childrenGoods entity.
     *
     * @Route("/{id}", name="childrengoods_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, childrenGoods $childrenGood)
    {
        $form = $this->createDeleteForm($childrenGood);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($childrenGood);
            $em->flush();
        }

        return $this->redirectToRoute('childrengoods_index');
    }

    /**
     * Creates a form to delete a childrenGoods entity.
     *
     * @param childrenGoods $childrenGood The childrenGoods entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(childrenGoods $childrenGood)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('childrengoods_delete', array('id' => $childrenGood->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     *
     *
     * @Route("/thanks", name="thanks")
     * @Method("GET")
     */
    public function thanksAction()
    {
        return $this->render('UserBundle::thanks.html.twig', array(
            //'childrenGood' => $childrenGood,
            //'delete_form' => $deleteForm->createView(),
            //s'add_new_cat' => $add_new_cat,
        ));
    }

    /**
     * Lists all childrenGoods entities.
     *
     * @Route("/calculate_cat_subcat", name="calculate_cat_subcat")
     * @Method("GET")
     */
    public function calculateAction()
    {

        $subCatAllId = array();

        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository('AdminBundle:childrenGoodsCategory')
                    ->findAll();

        foreach ($category as $key => $categoryOne) {
            $subCatOfCatAll = $categoryOne->getChildrenGoodsSubcategory();//->get(0)->getId();

            foreach ($subCatOfCatAll as $key => $subCatOfCatOne) {
                $subCatOfCatId = $subCatOfCatOne->getId();
                $subCatSub = $em->getRepository('AdminBundle:childrenGoodsSubcategory')
                    ->find($subCatOfCatId);
                $categoryOne->removeChildrenGoodsSubcategory($subCatSub);
            }
            
        }

        $em->flush();

        $repository = $em//$this->getDoctrine()
            ->getRepository('AdminBundle:childrenGoods');

        $query = $repository->createQueryBuilder('p')
            //->where('p.price > :price')
            //->setParameter('price', '19.99')
            ->orderBy('p.childrenGoodsCategory', 'ASC')
            ->getQuery();

        $childrenGoods = $query->getResult();

        $beforeId = $childrenGoods[0]->getChildrenGoodsCategory()->getId();
        $i = 0;
        $childrenGoodsCount = count($childrenGoods);

        for($j=0; $j<$childrenGoodsCount; $j++){
        //foreach ($childrenGoods as $key => $value) {

            var_dump($j);
            print "j, ";

                $idCat = $childrenGoods[$j]->getChildrenGoodsCategory()->getId();

            var_dump($idCat);
            print "idCat, ";
            var_dump($beforeId);
            print "beforeId, ";

            if ($idCat > $beforeId){

                $category = $em->getRepository('AdminBundle:childrenGoodsCategory')
                    ->find($beforeId);

                foreach ($subCatAllId as $key => $valueId) {  
                    $subCat = $em->getRepository('AdminBundle:childrenGoodsSubcategory')
                        ->find($valueId);
                    $category->addChildrenGoodsSubcategory($subCat);
                }

                $em->flush();

                $beforeId = $idCat;
                $i = 0;
                
                $subCatAllId = array();
            }

            $idSubcat = $childrenGoods[$j]->getChildrenGoodsSubcategory()->getId();
            if(array_search($idSubcat, $subCatAllId) === false){
                $subCatAllId[$i] = $idSubcat;
                $i++;
            }

            if($j == ($childrenGoodsCount-1) && count($subCatAllId)>0){
                var_dump($idCat);
                print "idCat, ";
                var_dump($beforeId);
                print "beforeId, ";
                $category = $em->getRepository('AdminBundle:childrenGoodsCategory')
                    ->find($beforeId);

                foreach ($subCatAllId as $key => $valueId) {  
                    $subCat = $em->getRepository('AdminBundle:childrenGoodsSubcategory')
                        ->find($valueId);
                    $category->addChildrenGoodsSubcategory($subCat);
                }

                $em->flush();

                    break;
            }
        }
        $childrenGoodsCategory = $childrenGoods[0]->getChildrenGoodsCategory();

        $childrenGoodsAllCategory = $em->getRepository('AdminBundle:childrenGoodsCategory')->findAll();

        return $this->render('UserBundle::calculateCatSubcat.html.twig', array(
            'childrenGoods' => $childrenGoods,
            'childrenGoodsCategory' => $childrenGoodsCategory,
            'subCatAllId' => $subCatAllId,
            'childrenGoodsAllCategory' => $childrenGoodsAllCategory,
        ));

    }

    /**
     * Debugger .
     *
     * @Route("/mydebug", name="mydebug")
     * @Method("GET")
     */
    public function mydebugAction()
    {

        return $this->render('UserBundle::mydebug.html.twig');
    }
}
