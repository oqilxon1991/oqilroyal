<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 21.11.2017
 * Time: 13:06
 */

namespace app\controllers;
use app\models\Category;
use app\models\Product;
use app\models\CommentForm;
use app\models\User;

use Yii;

class ProductController extends AppController{


    public function actionView($id){

        $product = Product::findOne($id);

        $comments = $product->getProductComments();
        $commentForm = new CommentForm();


        $hits = Product::find()->where(['hit' => '1'])->limit(4)->all();
        $this->setMeta('ROYALTECH.UZ | ' . $product->name, $product->keywords, $product->description);
        return $this->render('view', compact('product', 'hits', 'comments', 'commentForm'));
    }

    public function actionComment($id){

        $model = new CommentForm();

        if(Yii::$app->request->isPost){

            $model->load(Yii::$app->request->post());
//vd($model->attributes);
            if($model->saveComment($id)){
                Yii::$app->getSession()->setFlash('comment', 'Ваш отзыв принят, после проверки администратора Ваш отзыв появится.');
                return $this->redirect(['product/view', 'id'=>$id]);
            }
        }
    }
}
