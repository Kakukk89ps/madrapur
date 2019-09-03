<?php

namespace backend\modules\Reservations\models;

use backend\modules\MadActiveRecord\models\MadActiveRecord;
use backend\modules\Product\models\Product;
use backend\modules\Product\models\ProductTime;
use backend\modules\Tickets\models\TicketBlock;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class Reservations extends MadActiveRecord {

    const ACCESS_BOOKINGS_ADMIN = 'viewBookingsAdmin';
    const CREATE_BOOKING = 'createBooking';
    const EDIT_BOOKING = 'editBooking';
    const VIEW_BOOKINGS = 'viewBookings';
    const EDIT_OWN_BOOKING = 'editOwnBooking';
    const VIEW_OWN_BOOKINGS = 'viewOwnBookings';

    public static function primaryKey() {
        return ["id"];
    }

    public static function getList() {
        return ArrayHelper::map(self::find()->all(), 'bookingId', 'bookingDate');
    }

    public static function getCustomList($column = 'data') {
        return ArrayHelper::map(self::find()->all(), 'id', $column);
    }

    public static function getReservationById($id) {
        $reservation = Reservations::aSelect(Reservations::class, '*', Reservations::tableName(), 'id=' . $id);

        return $reservation->one();
    }

    public static function tableName() {
        return 'modulusBookings'; //TODO: dynamic table name
    }

    public static function getReservationsByIds($ids) {
        $reservation = Reservations::aSelect(Reservations::class, '*', Reservations::tableName(), 'id  IN (' . implode(",", $ids) . ')');

        return $reservation->all();
    }

    public static function getProductTimeshours($id) {
        $model = new ProductTime();
        $query = ($model->select('name', ProductTime::tableName(), ['=', 'product_id', $id]));
        //Thats how you query---^_-
        $mytimes = $query->all();

        $onlyHours = [];
        foreach ($mytimes as $time) {
            $onlyHours[] = $time['name'];
        }

        return $onlyHours;
    }

    public static function getProductTimes($id) {
        $query = ProductTime::aSelect(ProductTime::class, '*', ProductTime::tableName(), 'product_id=' . $id);
        $mytimes = $query->all();
        return $mytimes;
    }

    public function rules() {
        return [
            [['id'], 'integer'],
            [['bookingId'], 'integer'],
            [['source'], 'string', 'max' => 255],
            [['data'], 'string', 'max' => 1000],
            [['productId'], 'string', 'max' => 32],
            [['invoiceDate'], 'date', 'format' => 'yyyy-MM-dd'],
            [['invoiceMonth'], 'string', 'max' => 20],
            [['booking_cost'], 'string', 'max' => 100],
            [['bookingDate'], 'date', 'format' => 'yyyy-MM-dd'],
            [['sellerId'], 'string', 'max' => 255],
            [['sellerName'], 'string', 'max' => 255],
            [['booking_cost'], 'string', 'max' => 255],
            [['booking_product_id'], 'string', 'max' => 255],
            [['booking_start'], 'string', 'max' => 255],
            [['booking_end'], 'string', 'max' => 255],
            [['allPersons'], 'integer'],
            [['customer_ip_address'], 'string', 'max' => 255],
            [['paid_date'], 'string', 'max' => 255],
            [['billing_first_name'], 'string', 'max' => 255],
            [['billing_last_name'], 'string', 'max' => 255],
            [['billing_email'], 'string', 'max' => 255],
            [['billing_phone'], 'string', 'max' => 255],
            [['order_currency'], 'string', 'max' => 255],
            [['personInfo'], 'string'],
        ];
    }

    public function getFields() {
        return [
            'id',
            'bookingId',
            'source',
            'data',
            'productId',
            'invoiceDate',
            'bookingDate',
            'sellerId',
            "booking_cost",
            "booking_product_id",
            "booking_start",
            "booking_end",
            "allPersons",
            "customer_ip_address",
            "paid_date",
            "billing_first_name",
            "billing_last_name",
            "billing_email",
            "billing_phone",
            "order_currency",
            "personInfo",
        ];
    }

    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'bookingId' => Yii::t('app', 'BookingID'),
            'source' => Yii::t('app', 'Source'),
            'data' => Yii::t('app', 'JSON adat'),
            'productId' => Yii::t('app', 'Product Id'),
            'invoiceDate' => Yii::t('app', 'Invoice  date'),
            'bookingDate' => Yii::t('app', 'Booking date'),
            'sellerId' => Yii::t('app', 'Seller Id'),
        ];
    }

    public function attributes() {
        $attributes = parent::attributes();
        return array_merge($attributes, [
            'firstName', 'lastName', 'bookedChairsCount', 'bookingCost', 'orderCurrency', 'sourceName', 'updateDate',
            "booking_cost",
            "booking_product_id",
            "booking_start",
            "booking_end",
            "allPersons",
            "customer_ip_address",
            "paid_date",
            "billing_first_name",
            "billing_last_name",
            "billing_email",
            "billing_phone",
            "order_currency",
            "personInfo",
        ]);
    }

    public function afterFind() {

        parent::afterFind();

        $myjson = json_decode($this->data);

        if ($myjson && isset($myjson->orderDetails)) {

            if (isset($myjson->updateDate)) {

                $this->setAttribute("updateDate", $myjson->updateDate);
            }

            if (isset($myjson->orderDetails->edited_first_name)) {
                $this->setAttribute("firstName", $myjson->orderDetails->edited_first_name);
            } else {
                if (isset($myjson->orderDetails->billing_first_name)) {
                    $this->setAttribute("firstName", $myjson->orderDetails->billing_first_name);
                }
            }

            if (isset($myjson->orderDetails->edited_last_name)) {
                $this->setAttribute("lastName", $myjson->orderDetails->edited_last_name);
            } else {
                if (isset($myjson->orderDetails->billing_last_name)) {
                    $this->setAttribute("lastName", $myjson->orderDetails->billing_last_name);
                }
            }
            if (isset($myjson->orderDetails->edited_AllPersons)) {
                $this->setAttribute("bookedChairsCount", $myjson->orderDetails->edited_AllPersons);
            } else {
                if (isset($myjson->orderDetails->allPersons)) {
                    $this->setAttribute("bookedChairsCount", $myjson->orderDetails->allPersons);
                }
            }
            if (isset($myjson->boookingDetails->edited_booking_cost)) {
                $this->setAttribute("bookingCost", $myjson->boookingDetails->edited_booking_cost);
            } else {
                if (isset($myjson->boookingDetails)) {
                    $this->setAttribute("bookingCost", $myjson->boookingDetails->booking_cost);
                }
            }
            if (isset($myjson->orderDetails->edited_order_currency)) {
                $this->setAttribute("orderCurrency", $myjson->orderDetails->edited_order_currency);
            } else {
                if (isset($myjson->orderDetails->order_currency)) {

                    $this->setAttribute("orderCurrency", $myjson->orderDetails->order_currency);
                }
            }
            if (isset($myjson->orderDetails->edited_source_name)) {
                $this->setAttribute("sourceName", $myjson->orderDetails->edited_source_name);
            } else {
                if (isset($myjson->boookingDetails->booking_product_id)) {
                    $mySourceName = Product::searchSourceName($myjson->boookingDetails->booking_product_id, $this->source);
                    //   Yii::error('Mysourcename'.$mySourceName);
                    if ($mySourceName != null) {

                        $this->setAttribute("sourceName", $mySourceName);
                    } else {
                        // Yii::error('Mysourcename'.$mySourceName);
                        $this->setAttribute("sourceName", $this->source);
                    }
                }
            }
        } else {
            if (is_array($myjson)) {
                foreach ($myjson as $index => $json) {
                    // TODO: remove this if unnecessary
                }
            }
        }
    }

    public function beforeSave($insert) {

        /*Yii::$app->log($insert); Turn on for debug*/
        if (is_string($this->data)) {
            $myjson = Json::decode($this->data, false);
            $editableAttribute = Yii::$app->request->post('editableAttribute');

            switch ($editableAttribute) {
                case 'firstName':
                    $mystuff = Yii::$app->request->post('Reservations');
                    Yii::error($myjson);
                    $arraykey = array_key_first($mystuff);
                    $myjson->orderDetails->edited_first_name = $mystuff[$arraykey]['firstName'];
                    break;
                case 'lastName':
                    $mystuff = Yii::$app->request->post('Reservations');
                    $arraykey = array_key_first($mystuff);
                    $myjson->orderDetails->edited_last_name = $mystuff[$arraykey]['lastName'];
                    break;
                case 'bookedChairsCount':
                    $mystuff = Yii::$app->request->post('Reservations');
                    $arraykey = array_key_first($mystuff);
                    $myjson->orderDetails->edited_AllPersons = $mystuff[$arraykey]['bookedChairsCount'];

                    break;
                case 'bookingCost':
                    $mystuff = Yii::$app->request->post('Reservations');
                    if (isset($myjson->boookingDetails->edited_booking_cost)) {
                        $arraykey = array_key_first($mystuff);
                        $myjson->boookingDetails->edited_booking_cost = $mystuff[$arraykey]['bookingCost'];
                    }

                    break;
                case 'orderCurrency':
                    $mystuff = Yii::$app->request->post('Reservations');
                    $myjson->orderDetails->edited_order_currency = $mystuff[0]['order_currency'];
                    break;
                case 'sourceName':

                    $mystuff = Yii::$app->request->post('Reservations');
                    $myjson->orderDetails->edited_source_name = $mystuff[0]['sourceName'];
                    break;
                #Yii::warning('My posted Searchmodel'.\GuzzleHttp\json_encode($mystuff)->firstName);
            }

            $this->data = Json::encode($myjson);
            #var_dump($this->data);

            unset($this->firstName);
            unset($this->sourceName);
            unset($this->bookingCost);
            unset($this->orderCurrency);
            unset($this->lastName);
            unset($this->bookedChairsCount);
            unset($this->updateDate);
        }

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function getData() {
        return $this->data;
    }

    public function search($params) {
        $invoiceDate = '2016-02-05';
        $bookingDate = '2020-08-20';

        $what = ['*'];
        $from = self::tableName();

        $query = Reservations::find()->indexBy('id');;

        $query->andFilterWhere((['>=', 'invoiceDate', '2011-12-12']));
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        $query->andFilterWhere((['like', 'source', $this->source]));
        $query->andFilterWhere((['=', 'bookingDate', $this->bookingDate]));

        return $dataProvider;
    }

    public function searchMyreservations($params) {

        $what = ['*'];
        $from = self::tableName();
        $currentUserId = \Yii::$app->user->getId();

        $where = self::andWhereFilter([
            # ['invoiceDate', '>=', $invoiceDate],
            # ['bookingDate', '<=', $bookingDate],
            # ['source', '=', 'Street'],
            ['sellerId', '=', strval($currentUserId)],
        ]);

        $rows = self::aSelect(Reservations::class, $what, $from, $where);

        $dataProvider = new ActiveDataProvider([
            'query' => $rows,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        $this->load($params);

        return $dataProvider;
    }

    public function searchAllreservations($params) {

        $what = ['*'];
        $from = self::tableName();

        $searchParams = isset($params['Reservations']) ? $params['Reservations'] : [];
        $filters = [];
        $filters[] = ['source', 'IN', ['Street', 'Hotel']];

        foreach ($searchParams as $paramName => $paramValue) {
            if ($paramValue) {
                $filters[] = [$paramName, 'LIKE', $paramValue];
            }
        }

        $where = self::andWhereFilter($filters);

        $rows = self::aSelect(Reservations::class, $what, $from, $where, ['invoiceMonth' => SORT_ASC, 'source' => SORT_ASC, 'sellerName' => SORT_ASC, 'bookingId' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $rows,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        $this->load($params);

        return $dataProvider;
    }

    public function searchMonthlyStatistics($params) {
        $what = ['*'];
        $from = self::tableName();

        $searchParams = isset($params['Reservations']) ? $params['Reservations'] : [];
        $filters = [];

        foreach ($searchParams as $paramName => $paramValue) {
            if ($paramValue) {
                $filters[] = [$paramName, 'LIKE', $paramValue];
            }
        }

        $filters[] = ['order_currency', '=', 'EUR'];

        $where = self::andWhereFilter($filters);

        $orderBy = ['invoiceMonth' => SORT_ASC, 'source' => SORT_ASC, 'sellerName' => SORT_ASC, 'bookingId' => SORT_ASC];

        $rows = self::aSelect(
            Reservations::class,
            $what, $from, $where, $orderBy
        );

        $dataProvider = new ActiveDataProvider([
            'query' => $rows,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $this->load($params);

        return $dataProvider;
    }

    public function getMonthlyBySeller($sellerName) {
        $what = ['booking_cost'];
        $from = self::tableName();

        $filters = [];
        $filters[] = ['sellerName', '=', $sellerName];
        $filters[] = ['invoiceMonth', '=', date('m')];

        $where = self::andWhereFilter($filters);

        $rows = self::aSelect(
            Reservations::class,
            $what, $from, $where
        )->all();

        $cost = 0;
        foreach ($rows as $row) {
            $cost += $row->booking_cost;
        }

        return $cost;
    }

    public function getTodayBySeller($sellerName) {
        $what = ['booking_cost'];
        $from = self::tableName();

        $filters = [];
        $filters[] = ['sellerName', '=', $sellerName];
        $filters[] = ['invoiceDate', '=', date('Y-m-d')];

        $where = self::andWhereFilter($filters);

        $rows = self::aSelect(Reservations::class,
            $what, $from, $where
        )->all();

        $cost = 0;
        foreach ($rows as $row) {
            $cost += $row->booking_cost;
        }

        return $cost;
    }

    /**
     * @return array|Reservations|null|\yii\db\ActiveRecord
     */
    public function getTicketBookBySeller() {
        $what = ['startId'];
        $from = TicketBlock::tableName();

        $filters = [];
        $filters[] = ['assignedTo', '=', \Yii::$app->user->id];
        $filters[] = ['frozen', '=', 0];

        $where = self::andWhereFilter($filters);

        $row = self::aSelect(
            TicketBlock::class,
            $what, $from, $where
        )->one();

        return $row;
    }

    //TODO unused params

    public function searchDay($params, $selectedDate, $sources, $prodId) {

        $what = ['*'];
        $from = self::tableName();
        $wheres = [];
        $wheres[] = ['bookingDate', '=', $selectedDate];
        $sources[] = $prodId;
        $wheres[] = ['productId', 'IN', $sources];
        $where = self::andWhereFilter($wheres);
        $rows = self::aSelect(Reservations::class, $what, $from, $where);
        $dataProvider = new ActiveDataProvider([
            'query' => $rows,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);

        return $dataProvider;
    }

    public function searchDayTime($params, $selectedDate, $sources, $prodId, $time) {
        $what = ['*'];
        $from = self::tableName();
        $wheres = [];
        $wheres[] = ['bookingDate', '=', $selectedDate];
        $sources[] = $prodId;
        $wheres[] = ['productId', 'IN', $sources];
        $wheres[] = ['booking_start', '=', $selectedDate . ' ' . $time . ':00'];
        $where = self::andWhereFilter($wheres);
        $rows = self::aSelect(Reservations::class, $what, $from, $where);
        $dataProvider = new ActiveDataProvider([
            'query' => $rows,
            'pagination' => [
                'pageSize' => 15,
            ],
        ]);
        $this->load($params);
        return $dataProvider;
    }

    public function countTakenChairsOnDay($selectedDate, $sources) {

        $what = ['*'];
        $from = self::tableName();
        $wheres = [];
        $wheres[] = ['bookingDate', '=', $selectedDate];
        $wheres[] = ['productId', 'IN', $sources];
        $where = self::andWhereFilter($wheres);
        $rows = self::aSelect(Reservations::class, $what, $from, $where);
        $bookigsFromThatDay = $rows->all();
        $counter = 0;
        foreach ($bookigsFromThatDay as $reservation) {
            if (isset($reservation->bookedChairsCount)) {
                $counter = $counter + $reservation->bookedChairsCount;
                if ($reservation->bookedChairsCount % 2 === 1) {
                    $counter += 1;
                }
            }
        }
        return $counter;
    }

    public function countTakenChairsOnDayTime($selectedDate, $sources, $time) {

        $what = ['*'];
        $from = self::tableName();
        $wheres = [];
        $wheres[] = ['bookingDate', '=', $selectedDate];
        $wheres[] = ['productId', 'IN', $sources];
        $wheres[] = ['booking_start', '=', $selectedDate . ' ' . $time . ':00'];
        $where = self::andWhereFilter($wheres);
        $rows = self::aSelect(Reservations::class, $what, $from, $where);
        $bookigsFromThatDay = $rows->all();
        $counter = 0;
        foreach ($bookigsFromThatDay as $reservation) {
            if (isset($reservation->bookedChairsCount)) {
                $counter = $counter + $reservation->bookedChairsCount;
                if ($reservation->bookedChairsCount % 2 === 1) {
                    $counter += 1;
                }
            }
        }
        return $counter;
    }

    public function availableChairsOnDay($params, $selectedDate, $sources, $prodId) {

        $what = ['*'];
        $from = self::tableName();
        $wheres = [];
        $wheres[] = ['bookingDate', '=', $selectedDate];
        $wheres[] = ['productId', 'IN', $sources];
        $where = self::andWhereFilter($wheres);
        $rows = self::aSelect(Reservations::class, $what, $from, $where);
        $bookigsFromThatDay = $rows->all();
        $counter = 0;
        foreach ($bookigsFromThatDay as $reservation) {
            if (isset($reservation->bookedChairsCount)) {
                $counter = $counter + $reservation->bookedChairsCount;
                if ($reservation->bookedChairsCount % 2 === 1) {
                    $counter += 1;
                }
            }
        }
        $currentProduct = Product::getProdById($prodId);
        $placesleft = $currentProduct->capacity - $counter;
        if ($placesleft % 2 != 0) {
            $placesleft -= 1;
        }
        return $placesleft;
    }

    public function searchChart($params) {
        $invoiceDate = '2016-02-05';
        $bookingDate = '2020-08-20';

        $what = ['*'];
        $from = self::tableName();
        $where = self::andWhereFilter([
            ['invoiceDate', '>=', $invoiceDate],
            ['bookingDate', '<=', $bookingDate]
            # ['source', 'LIKE', 'utca']
        ]);

        $rows = self::aSelect(Reservations::class, $what, $from, $where);

        $dataProvider = new ActiveDataProvider([
            'query' => $rows,
        ]);

        $this->load($params);

        return $dataProvider;
    }

    public function searchChartForStats($startDate = null, $endDate = null, $source = null) {
        $what = ['*'];
        $from = self::tableName();
        $where = self::andWhereFilter([
            ['invoiceDate', '>=', $startDate],
            ['invoiceDate', '<=', $endDate],
            # ['source', 'LIKE', 'utca']
        ]);

        $rows = self::aSelect(Reservations::class, $what, $from, $where);

        $dataProvider = new ActiveDataProvider([
            'query' => $rows,
        ]);

        return $dataProvider;
    }

    public function returnBookingId() {
        return $this['bookingId'];
    }

    public function returnId() {
        return $this['id'];
    }

}