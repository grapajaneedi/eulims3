<?php

namespace frontend\modules\finance\controllers;

use Yii;
use common\models\finance\Orderofpayment;
use common\models\finance\Paymentitem;
use common\models\finance\OrderofpaymentSearch;
use common\models\lab\Request;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use common\components\Functions;
/**
 * OrderofpaymentController implements the CRUD actions for Orderofpayment model.
 */
class OrderofpaymentController extends Controller
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
     * Lists all Orderofpayment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Orderofpayment();
        $searchModel = new OrderofpaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Orderofpayment model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    { 
         $paymentitem_Query = Paymentitem::find()->where(['orderofpayment_id' => $id]);
         $paymentitemDataProvider = new ActiveDataProvider([
                'query' => $paymentitem_Query,
                'pagination' => [
                    'pageSize' => 10,
                ],
        ]);
         
         return $this->render('view', [
            'model' => $this->findModel($id),
            'paymentitemDataProvider' => $paymentitemDataProvider,
        ]);

    }

    /**
     * Creates a new Orderofpayment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Orderofpayment();
        $searchModel = new OrderofpaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize=5;
        
        //echo "<pre>";
        //print_r($this->Gettransactionnum());
        //echo "</pre>";
        
         if ($model->load(Yii::$app->request->post())) {
                $session = Yii::$app->session;
                $request_ids=$model->RequestIds;
                $str_request = explode(',', $request_ids);
                $model->rstl_id='1';
                $model->transactionnum= $this->Gettransactionnum();
              
                $model->save();
               
                $arr_length = count($str_request); 
                for($i=0;$i<$arr_length;$i++){
                     $request =$this->findRequest($str_request[$i]);
                     $paymentitem = new Paymentitem();
                     $paymentitem->rstl_id = 1;
                     $paymentitem->request_id = $str_request[$i];
                     $paymentitem->orderofpayment_id = $model->orderofpayment_id;
                     $paymentitem->details =$request->request_ref_num;
                     $paymentitem->amount = $request->total;
                     $paymentitem->save(); 
                }
                $session->set('savepopup',"executed");
                return $this->redirect(['/finance/orderofpayment']);
          
        } 
        if(Yii::$app->request->isAjax){
            return $this->renderAjax('create', [
                'model' => $model,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }else{
            return $this->render('create', [
                'model' => $model,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        }
        
    }

    /**
     * Updates an existing Orderofpayment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->orderofpayment_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

     public function actionGetlistrequest($id)
    {
        $query = Request::find()->where(['customer_id' => $id]);
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $dataProvider->pagination->pageSize=3;
        if(Yii::$app->request->isAjax){
            return $this->renderAjax('_request', ['dataProvider'=>$dataProvider]);
        }

    }
    /**
     * Deletes an existing Orderofpayment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Orderofpayment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Orderofpayment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Orderofpayment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    protected function findRequest($requestId)
    {
        if (($model = Request::findOne($requestId)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
     public function Gettransactionnum(){
        $year=date('Y');
        $terminal_id=1;
        $rstl_id=11;
        $result = \Yii::$app->financedb->createCommand("CALL spGenerateTransactionNumber(:mYear, :mTerminalID, :mRSTLID)",[
            ':mYear'=>$year,
            ':mTerminalID'=>$terminal_id,
            ':mRSTLID'=>$rstl_id
            ])
            ->queryAll();
        $transnumber=$result[0]['transaction_number'];
        return $transnumber;
        
     }
}
