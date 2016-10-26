<?php

namespace UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AdminBundle\Entity\childrenGoods;
use AdminBundle\Entity\childrenGoodsCategory;
use AdminBundle\Entity\childrenGoodsSizeNumber;
use UserBundle\Entity\bagRegisrtr;
use AdminBundle\Form\childrenGoodsType;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * indexUserController controller.
 *
 * 
 */
class indexUserController extends Controller
{
    /**
     * Lists all childrenGoods entities.
     *
     * @Route("/", name="index_user")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $childrenGoods = $em->getRepository('AdminBundle:childrenGoods')->findAll();
        $childrenGoodsCategory = $em->getRepository('AdminBundle:childrenGoodsCategory')->findAll();

        return $this->render('UserBundle::indexUser.html.twig', array(
            'childrenGoods' => $childrenGoods,
            'childrenGoodsCategory' => $childrenGoodsCategory,
        ));
    }

    /**
     * Finds and displays a childrenGoods entity.
     *
     * @Route("/{id}", name="user_show")
     * @Method("GET")
     */
    public function showAction(childrenGoods $childrenGood, Request $request)
    {
        /*$add_new_cat = 'nety';
        if(isset($_GET["add_new_cat"])){
                //$add_new_cat = $_GET["add_new_cat"];
                $add_new_cat = $request->query->get('add_new_cat');
            }*/
        

        $deleteForm = $this->createDeleteForm($childrenGood);

        return $this->render('UserBundle::showGood.html.twig', array(
            'childrenGood' => $childrenGood,
            'delete_form' => $deleteForm->createView(),
            //s'add_new_cat' => $add_new_cat,
        ));
    }

    /**
     * Finds and displays a childrenGoods entity.
     *
     * @Route("/{children_goods_category_id}/{children_goods_subcategory_id}", name="cat_sub_show")
     * @Method("GET")
     */
    public function showSubcatAction($children_goods_category_id, $children_goods_subcategory_id )// ,$children_goods_category_id, $children_goods_subcategory_id  {children_goods_category_id}/{children_goods_subcategory_id}
    {
        /*$add_new_cat = 'nety';
        if(isset($_GET["add_new_cat"])){
                //$add_new_cat = $_GET["add_new_cat"];
                $add_new_cat = $request->query->get('add_new_cat');
            }*/
        

        //$deleteForm = $this->createDeleteForm($childrenGood);

        $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository('AdminBundle:childrenGoods');

        $category = $em->getRepository('AdminBundle:childrenGoodsCategory')
                    ->findOneById($children_goods_category_id);

        $subcategory = $em->getRepository('AdminBundle:childrenGoodsSubcategory')
                    ->findOneById($children_goods_subcategory_id);

        $query = $repository->createQueryBuilder('p')
            ->where('p.childrenGoodsCategory = :children_goods_category_id AND p.childrenGoodsSubcategory = :children_goods_subcategory_id')
            ->setParameter('children_goods_category_id', $category)
            ->setParameter('children_goods_subcategory_id', $subcategory)
            //->setParameter(array(1 => $category, 2 => $subcategory))
            ->orderBy('p.title', 'ASC')
            ->getQuery();

        $childrenGoods = $query->getResult();

        return $this->render('UserBundle::showSubcat.html.twig', array(
            'childrenGoods' => $childrenGoods//$childrenGood,
            //'delete_form' => $deleteForm->createView(),
            //s'add_new_cat' => $add_new_cat,
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
     * Creates a new childrenGoods entity.
     *
     * @Route("/bag_registr", name="bag_registr")
     * @Method({"GET", "POST"})
     */
    public function BagRegistrAction(Request $request)
    {
        //$session = new Session();

        $bagRegisrtr = new bagRegisrtr();
        /*$childrenGoodPer = new childrenGoods();

        $childrenGoodsSizeNumber = new childrenGoodsSizeNumber();

        $childrenGoodSession = "nety";
        if($session->get('childrenGood') != null){
            $childrenGoodSession = $session->get('childrenGood');
            $childrenGood->setTitle($childrenGoodSession->getTitle());
            $childrenGood->setPosition($childrenGoodSession->getPosition());
            $childrenGood->setProdDatetime($childrenGoodSession->getProdDatetime());
            $childrenGood->setProdDatetimeUpdate($childrenGoodSession->getProdDatetimeUpdate());
            //$admin_childrenGood->getChildrenGoodsSizeNumber()->get(0)->getSizegoods()->getSize();
            $session-> invalidate();
        }
        else{
            $childrenGood->setProdDatetime(new \DateTime('tomorrow'));
            $childrenGood->setProdDatetimeUpdate(new \DateTime('tomorrow'));
        }*/

        /*$tag1 = new Tag();
        $tag1->setName('tag1');
        $task->getTags()->add($tag1);
        $tag2 = new Tag();
        $tag2->setName('tag2');
        $task->getTags()->add($tag2);

        $em = $this->getDoctrine()->getManager();

        $size = $em->getRepository('AdminBundle:size')->findAll();*/

        $form = $this->createForm('UserBundle\Form\bagRegistrType', $bagRegisrtr);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($bagRegisrtr);
            $em->flush();
            //if($_POST["add_new_cat"] == 'true'){
                //$add_new_cat = $_POST["add_new_cat"];
                //$session->set('childrenGood', $childrenGood);
                //$session->set('childrenGoodId', $childrenGood);
                return $this->render('UserBundle::thanks.html.twig', array(
                    //'childrenGood' => $childrenGood,
                    'form' => $form->createView(),
                    //'formSizeNumber' => $formSizeNumber->createView(),
                    //'childrenGoodSession' => $childrenGoodSession,
                )); 
            //}

            //return $this->redirectToRoute('childrengoods_show', 
            //        array('id' => 1,));

            //$childrenGoodMy = $_POST["children_goods"];
            //$childrenGoodPer->setTitle($childrenGoodMy['priceGoods']);
            //$childrenGoodPer->setPosition($childrenGoodMy['position']);
            //$childrenGoodPer->setProdDatetime($childrenGoodMy['prodDatetime']);
            //$childrenGoodPer->setProdDatetimeUpdate($childrenGoodMy['prodDatetimeUpdate']['date']);
            //$childrenGoodPer->setProdDatetime(new \DateTime('tomorrow'));
            //$childrenGoodPer->setProdDatetimeUpdate(new \DateTime('tomorrow'));

            //$em = $this->getDoctrine()->getManager();
            //$em->persist($childrenGoodPer);
            //$em->flush();
            //$childrenGoodsPer = $em->getRepository('AdminBundle:childrenGoods')->findAll();

            //$childrenGoodId = $childrenGoodPer->getId();

            /*foreach ($childrenGoodMy['childrenGoodsSizeNumber'] as $key => $value) {
                $childrenGoodsSizeNumberMy = new childrenGoodsSizeNumber();
                $childrenGoodsSizeNumberMy -> setChildrenGoods($value);
            }*/

            //return $this->render('childrengoods/myshow.html.twig', array(
              //  'childrenGood' => $childrenGood,
                //'childrenGoodMy' => $childrenGoodMy,
                //'childrenGoodId' => $childrenGoodId,
                //'childrenGoodPer' => $childrenGoodPer,
            //));

            /*if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($childrenGood);
                $em->flush();

                return $this->redirectToRoute('childrengoods_show', 
                    array('id' => $childrenGood->getId(),
                        //'add_new_cat' => $add_new_cat,
                        ));
            }*/
        }

        return $this->render('UserBundle::bagRegisrtr.html.twig', array(
            //'childrenGood' => $childrenGood,
            //'form' => $form->createView(),
            //'formSizeNumber' => $formSizeNumber->createView(),
            //'childrenGoodSession' => $childrenGoodSession,
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

        //$subCat = $em->getRepository('AdminBundle:childrenGoodsSubcategory');

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

        /*foreach ($subCatAllId as $key => $valueId) {  
            $category->addChildrenGoodsSubcategory($subCat);
        }*/

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
                //break;
                //$category = new childrenGoodsCategory();

                $category = $em->getRepository('AdminBundle:childrenGoodsCategory')
                    ->find($beforeId);

                foreach ($subCatAllId as $key => $valueId) {  
                    $subCat = $em->getRepository('AdminBundle:childrenGoodsSubcategory')
                        ->find($valueId);
                    $category->addChildrenGoodsSubcategory($subCat);
                }

                //$category->setChildrenGoodsSubcategory($subCatAllId); 

                //$em->persist($category);
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

            //var_dump($beforeId);
            //print "beforeId, ";
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

                //$category->setChildrenGoodsSubcategory($subCatAllId); 

                //$em->persist($category);
                $em->flush();

                    break;
            }
        }
        $childrenGoodsCategory = $childrenGoods[0]->getChildrenGoodsCategory();

        //$childrenGoods = $em->getRepository('AdminBundle:childrenGoods')->findByChildrenGoodsCategory(1);
        $childrenGoodsAllCategory = $em->getRepository('AdminBundle:childrenGoodsCategory')->findAll();

        return $this->render('UserBundle::calculateCatSubcat.html.twig', array(
            'childrenGoods' => $childrenGoods,
            'childrenGoodsCategory' => $childrenGoodsCategory,
            'subCatAllId' => $subCatAllId,
            'childrenGoodsAllCategory' => $childrenGoodsAllCategory,
        ));

        //return $this;
    }
}
