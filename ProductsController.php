<?php

namespace app\controllers;

use Yii;
use app\models\Product;
use app\models\Category;
use app\models\ProductSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;

/**
 * ProductsController implements the CRUD actions for Product model.
 */
class ProductsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Product models.
     * @return mixed
     */


    public function actionIndex($category_id = null)
    {
        if(is_null($category_id)){
            $get = Yii::$app->request->queryParams;
            $category_id = $get['ProductSearch']['category_id'];
        }

        $category = Category::findOne($category_id);
        $undercat = Category::find()->where(['parent_id' => $category_id])->all();
        $query = Product::find()->where(['category_id' => $category_id]);
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => 15, 'forcePageParam' => false, 'pageSizeParam' => false]);
        //vd($query);

//vd($query));
        $searchModel = new ProductSearch();
        if($category->parent_id == 0){
            $product = new Product();
            $products = $product->getProductsByParentCategory($category_id);
            //$products = new ActiveDataProvider(['query'=>$product->getProductsByParentCategory($category_id)]);
            //vd($products);
        }else{

            $products = $query->offset($pages->offset)->limit($pages->limit);
            $dataProvider = new ActiveDataProvider([
                'query' => $products,
            ]);
            //$products = new ActiveDataProvider(['query'=>$query->offset($pages->offset)->limit($pages->limit)->all()]);
        }
//vd($dataProvider);
        /*$searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);*/
//vd(Yii::$app->request->queryParams);
        //$model->category_id = $category_id;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'category' => $category,
            'undercat' => $undercat,
            //'products' => $products,
        ]);



    }

    public function actionView($id){
        $category = Category::findOne($id);
        if(empty($category))
            throw new \yii\web\HttpException(404, 'Такой категории нет');

        $query = Product::find()->where(['category_id' => $id]);
        /*$pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => 15, 'forcePageParam' => false, 'pageSizeParam' => false]);*/


        if($category->parent_id == 0){
            $product = new Product();
            $products = $product->getProductsByParentCategory($id);
            //$products = new ActiveDataProvider(['query'=>$product->getProductsByParentCategory($id)]);
            //vd($products);
        }else{
            $products = $query->offset($pages->offset)->limit($pages->limit)->all();
            //$products = new ActiveDataProvider(['query'=>$query->offset($pages->offset)->limit($pages->limit)->all()]);
        }

        $this->setMeta('ROYALTECH | ' . $category->name, $category->keywords, $category->description);
        return $this->render('view', compact('products', 'pages', 'category'));
    }

    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Product();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
