<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 18.11.2017
 * Time: 18:54
 */

namespace app\controllers;
use app\models\Category;
use app\models\Product;
use Yii;
use yii\data\Pagination;
use yii\jui\Slider;

class NewproductsController extends AppController{

    public function actionIndex(){
        $news = Product::find()->where(['new' => '1'])->all();
        $pages = new Pagination(['pageSize' => 6, 'forcePageParam' => false, 'pageSizeParam' => false]);
        $this->setMeta('ROYALTECH | Хиты продаж!');
        return $this->render('index', compact('pages', 'news'));
    }


}