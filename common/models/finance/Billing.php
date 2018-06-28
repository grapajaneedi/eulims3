<?php

namespace common\models\finance;

use Yii;

/**
 * This is the model class for table "tbl_billing".
 *
 * @property int $billing_id
 * @property int $user_id
 * @property int $customer_id
 * @property string $soa_number
 * @property string $billing_date
 * @property string $due_date
 * @property int $receipt_id
 * @property string $amount
 *
 * @property Receipt $receipt
 * @property OpBilling[] $opBillings
 */
class Billing extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_billing';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('financedb');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'customer_id', 'billing_date', 'due_date'], 'required'],
            [['user_id', 'customer_id', 'receipt_id'], 'integer'],
            [['billing_date', 'due_date'], 'safe'],
            [['amount'], 'number'],
            ['amount', 'compare', 'compareValue' => 0, 'operator' => '>'],
            [['soa_number','invoice_number'], 'string', 'max' => 100],
            [['receipt_id'], 'exist', 'skipOnError' => true, 'targetClass' => Receipt::className(), 'targetAttribute' => ['receipt_id' => 'receipt_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'billing_id' => 'Billing ID',
            'invoice_number' => 'Invoice #', 
            'user_id' => 'User',
            'customer_id' => 'Customer',
            'soa_number' => 'Soa Number',
            'billing_date' => 'Billing Date',
            'due_date' => 'Due Date',
            'receipt_id' => 'Receipt ID',
            'amount' => 'Amount',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceipt()
    {
        return $this->hasOne(Receipt::className(), ['receipt_id' => 'receipt_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOpBillings()
    {
        return $this->hasMany(OpBilling::className(), ['billing_id' => 'billing_id']);
    }
}
