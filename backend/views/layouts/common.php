<?php
/**
 * @var $this yii\web\View
 * @var $content string
 */
use backend\assets\BackendAsset;
use backend\modules\system\models\SystemLog;
use backend\widgets\Menu;
use common\models\TimelineEvent;
    use kartik\icons\Icon;
    use kartik\popover\PopoverXAsset;
    use yii\bootstrap4\Alert;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\log\Logger;
use yii\widgets\Breadcrumbs;

\kartik\dialog\DialogBootstrapAsset::register($this);

$bundle = BackendAsset::register($this);


    $sidemenu=   [
        'options' => [
            'class' => 'nav nav-pills nav-sidebar flex-column',
            'data-widget' => 'treeview',
            'role'=>'menu',
            'data-accordion'=>false
        ],
        'linkTemplate' => '<a class="nav-link" href="{url}">{icon}<span>{label}</span>{badge}{right-icon}</a>',
        'submenuTemplate' => "\n<ul class=\"nav nav-treeview \">\n{items}\n</ul>\n",
        'activateParents' => true,
        'itemOptions'=>['class'=>'nav-item'],
        'items' => [
            // MAIN
            [
                'label' => Yii::t('backend', 'Main'),
                'options' => [
                    'class' => 'nav-header'
                ],
            ],

            [
                'label' => Yii::t('backend', 'Dashboard'),
                'icon' => Icon::show('columns', [ 'class'=>'nav-icon','framework'=> Icon::FAS]),
                'url' => [
                    '/Dashboard/dashboard/admin'
                ],
                'options' => [
                    'class' => 'nav-item'
                ],
            ],
            [
                'label' => Yii::t('backend', 'Timeline'),
                'icon' => Icon::show('chart-bar', ['class'=>'nav-icon', 'framework'=> Icon::FAS]),
                'url' => [
                    '/timeline-event/index'
                ],
                'badge' => TimelineEvent::find()->today()->count(),
                'badgeClass' => 'badge badge-info right',
                'visible'=>Yii::$app->user->can('administrator'),
                'options' => [
                    'class' => 'nav-item'
                ],

            ],
            [
                'label' => Yii::t('backend', 'adminTools'),
                'icon' => Icon::show('smile', ['class'=>'nav-icon', 'framework'=> Icon::FAS]),
                'url'=>['#'],
                'options' => [
                    'class' => 'nav-item has-treeview'
                ],
                'items'=>[
                    [
                        'label' => Yii::t('backend', 'modMail'),
                        'icon' => '<i class="fa nav-icon fa-envelope-o"></i>',
                        'url' => [
                            '/Modmail/modmail/admin'
                        ],

                    ],
                    [
                        'label' => Yii::t('backend', 'Db editor'),
                        'icon' => '<i class="fa nav-icon fa-table"></i>',
                        'url' => [
                            '/dbeditor.php?username=root2&db=mad_dev-db'
                        ],

                    ],
                ],
                'visible'=>Yii::$app->user->can('administrator')
            ],
            [
                'label' => Yii::t('backend', 'Users'),
                'icon' => Icon::show('users', ['class'=>'nav-icon', 'framework'=> Icon::FAS]),
                'url' => [
                    '/user/index'
                ],
                'options' => [
                    'class' => 'nav-item'
                ],
                'active' => Yii::$app->controller->id === 'user',
                'visible' => Yii::$app->user->can('accessUsers'),
            ],

            [
                'label' => Yii::t('backend', 'Tickets'),
                'url' => [
                    '/Tickets/tickets/admin'
                ],
                'options' => [
                 'class' => 'nav-item has-treeview'
                ],
                'icon' => Icon::show('ticket-alt', ['class'=>'nav-icon', 'framework'=> Icon::FAS]),
                'items' => [
                    [
                        'label' => Yii::t('backend', 'Summary'),
                        'url' => [
                            '/Tickets/tickets/admin'
                        ],
                        'icon' => '<i class="fa nav-icon fa-list-alt"></i>',
                        'active' => Yii::$app->controller->id === 'tickets' &&
                            Yii::$app->controller->action->id === 'admin',
                        'visible' => Yii::$app->user->can('accessTicketsAdmin'),
                    ],
                    [
                        'label' => Yii::t('backend', 'Add ticket block'),
                        'url' => [
                            '/Tickets/tickets/add-block'
                        ],
                        'icon' => '<i class="fa nav-icon fa-plus"></i>',
                        'active' => Yii::$app->controller->id === 'tickets' &&
                            Yii::$app->controller->action->id === 'add-block',
                        'visible' => Yii::$app->user->can('addTicketBlock'),
                    ],
                    [
                        'label' => Yii::t('backend', 'View assigned blocks'),
                        'url' => [
                            '/Tickets/tickets/view-assigned-blocks'
                        ],
                        'icon' => '<i class="fa nav-icon fa-"></i>',
                        'active' => Yii::$app->controller->id === 'tickets' &&
                            Yii::$app->controller->action->id === 'view-assigned-blocks',
                        'visible' => Yii::$app->user->can('assignTicketBlock') && Yii::$app->user->can('administrator'),
                    ],
                ],
                'active' => Yii::$app->controller->id === 'tickets',
                'visible' => Yii::$app->user->can('accessTickets'),
            ],

            // CONTENT
            [
                'label' => Yii::t('backend', 'Content'),
                'options' => [
                    'class' => 'nav-header'
                ],
                'visible' => Yii::$app->user->can('accessContent') && Yii::$app->user->can('administrator'),
            ],
            [
                'label' => Yii::t('backend', 'Email templates'),
                'url' => [
                    '/Modulusbuilder/modulusbuilder/email'
                ],
                'options' => [
                    'class' => 'nav-item '
                ],
                'icon' => '<i class="fa nav-icon fa-envelope"></i>',
                'active' => Yii::$app->controller->id === 'modulusbuilder' &&
                    Yii::$app->controller->action === 'email' && Yii::$app->user->can('administrator'),
                'visible' => !Yii::$app->user->can('streetAdmin') && !Yii::$app->user->can('streetSeller') && !Yii::$app->user->can('hotline'),
            ],
            [
                'label' => Yii::t('backend', 'Static pages'),
                'url' => [
                    '/content/page/index'
                ],
                'icon' => Icon::show('pager', [ 'class'=>'nav-icon','framework'=> Icon::FAS]),
                'active' => Yii::$app->controller->id === 'page',
                'visible' => Yii::$app->user->can('accessContent') && Yii::$app->user->can('administrator'),
                'options' => [
                    'class' => 'nav-item'
                ],
            ],
            [
                'label' => Yii::t('backend', 'Articles'),
                'url' => [
                    '/content/article/index'
                ],
                'icon' => Icon::show('newspaper', [ 'class'=>'nav-icon','framework'=> Icon::FAS]),
                'options' => [
                 'class' => 'nav-item has-treeview'
                ],
                'active' => 'content' === Yii::$app->controller->module->id &&
                    ('article' === Yii::$app->controller->id || 'category' === Yii::$app->controller->id),
                'items' => [
                    [
                        'label' => Yii::t('backend', 'View Articles'),
                        'url' => [
                            '/content/article/index'
                        ],
//                        'icon' => Icon::showStack('newspaper nav-icon', 'edit  nav-icon'),

                        'icon' => Icon::show('newspaper', [ 'class'=>'nav-icon','framework'=> Icon::FAS]),
                        'active' => Yii::$app->controller->id === 'article',
                    ],
                    [
                        'label' => Yii::t('backend', 'Article Categories'),
                        'url' => [
                            '/content/category/index'
                        ],
                        'icon' => Icon::show('newspaper', [ 'class'=>'nav-icon','framework'=> Icon::FAS]),
                        'active' => Yii::$app->controller->id === 'category',
                    ],
                ],
                'visible' => Yii::$app->user->can('accessContent'),
            ],
            [
                'label' => Yii::t('backend', 'Widgets'),
                'url' => [
                    '/content/page/index'
                ],
                'icon' => '<i class="fa nav-icon fa-code"></i>',
                'options' => [
                 'class' => 'nav-item has-treeview'
                ],
                'active' => Yii::$app->controller->module->id === 'widget',
                'items' => [
                    [
                        'label' => Yii::t('backend', 'Text Blocks'),
                        'url' => [
                            '/widget/text/index'
                        ],
                        'icon' => '<i class="fa nav-icon fa-circle-o"></i>',
                        'active' => Yii::$app->controller->id === 'text',
                    ],
                    [
                        'label' => Yii::t('backend', 'Menu'),
                        'url' => [
                            '/widget/menu/index'
                        ],
                        'icon' => '<i class="fa nav-icon fa-circle-o"></i>',
                        'active' => Yii::$app->controller->id === 'menu',
                    ],
                    [
                        'label' => Yii::t('backend', 'Carousel'),
                        'url' => [
                            '/widget/carousel/index'
                        ],
                        'icon' => '<i class="fa nav-icon fa-circle-o"></i>',
                        'active' => in_array(Yii::$app->controller->id, [
                            'carousel',
                            'carousel-item'
                        ]),
                    ],
                ],
                'visible' => Yii::$app->user->can('accessContent'),
            ],

            // TRANSLATION
            [
                'label' => Yii::t('backend', 'Translation'),
                'options' => [
                    'class' => 'nav-header'
                ],
                'visible' => Yii::$app->user->can('accessTranslation'),
            ],
            [
                'label' => Yii::t('backend', 'Translation'),
                'url' => [
                    '/translation/default/index'
                ],
                'icon' => '<i class="fa nav-icon fa-language"></i>',
                'active' => (Yii::$app->controller->module->id == 'translation'),
                'visible' => Yii::$app->user->can('accessTranslation'),
            ],

            // SYSTEM
            [
                'label' => Yii::t('backend', 'System'),
                'options' => [
                    'class' => 'nav-header'
                ],
                'visible' => Yii::$app->user->can('accessSystem'),
            ],
            [
                'label' => Yii::t('backend', 'RBAC Rules'),
                'url' => [
                    '/rbac/rbac-auth-rule/index'
                ],
                'icon' => '<i class="fa nav-icon fa-flag"></i>',
                'options' => [
                 'class' => 'nav-item has-treeview'
                ],
                'active' => in_array(Yii::$app->controller->id,
                    [
                        'rbac-auth-assignment',
                        'rbac-auth-item',
                        'rbac-auth-item-child',
                        'rbac-auth-rule'
                    ]),
                'items' => [
                    [
                        'label' => Yii::t('backend', 'Auth Assignment'),
                        'url' => [
                            '/rbac/rbac-auth-assignment/index'
                        ],
                        'icon' => '<i class="fa nav-icon fa-circle-o"></i>',
                    ],
                    [
                        'label' => Yii::t('backend', 'Auth Items'),
                        'url' => [
                            '/rbac/rbac-auth-item/index'
                        ],
                        'icon' => '<i class="fa nav-icon fa-circle-o"></i>',
                    ],
                    [
                        'label' => Yii::t('backend', 'Auth Item Child'),
                        'url' => [
                            '/rbac/rbac-auth-item-child/index'
                        ],
                        'icon' => '<i class="fa nav-icon fa-circle-o"></i>',
                    ],
                    [
                        'label' => Yii::t('backend', 'Auth Rules'),
                        'url' => [
                            '/rbac/rbac-auth-rule/index'
                        ],
                        'icon' => '<i class="fa nav-icon fa-circle-o"></i>',
                    ],
                ],
                'visible' => Yii::$app->user->can('accessRBAC'),
            ],
            [
                'label' => Yii::t('backend', 'Files'),
                'url' => '#',
                'icon' => '<i class="fa nav-icon fa-th-large"></i>',
                'options' => [
                 'class' => 'nav-item has-treeview'
                ],
                'active' => (Yii::$app->controller->module->id == 'file'),
                'items' => [
                    [
                        'label' => Yii::t('backend', 'Storage'),
                        'url' => [
                            '/file/storage/index'
                        ],
                        'icon' => '<i class="fa nav-icon fa-database"></i>',
                        'active' => (Yii::$app->controller->id == 'storage'),
                    ],
                    [
                        'label' => Yii::t('backend', 'Manager'),
                        'url' => [
                            '/file/manager/index'
                        ],
                        'icon' => '<i class="fa nav-icon fa-television"></i>',
                        'active' => (Yii::$app->controller->id == 'manager'),
                    ],
                ],
                'visible' => Yii::$app->user->can('accessFiles'),
            ],
            [
                'label' => Yii::t('backend', 'Products'),
                'options' => [
                 'class' => 'nav-item has-treeview'
                ],
                'url' => [

                    '/Product/product/admin'
                ],
                'options' => [
                 'class' => 'nav-item has-treeview'
                ],
                'icon' =>  Icon::show('boxes', [ 'class'=>'nav-icon','framework'=> Icon::FAS]),
                'active' => Yii::$app->controller->id === 'product' &&
                    Yii::$app->controller->action->id !== 'uiblock',
                'items' => [
                    [
                        'label' => Yii::t('backend', 'Overview'),
                        'url' => [
                            '/Product/product/admin'
                        ],
                        'icon' => '<i class="fa nav-icon fa-list-alt"></i>',
                        'active' => (Yii::$app->controller->id == 'product') &&
                            Yii::$app->controller->action->id === 'admin',
                    ],
                    [
                        'label' => Yii::t('backend', 'Create'),
                        'url' => [
                            '/Product/product/create'
                        ],
                        'icon' => '<i class="fa nav-icon fa-database"></i>',
                        'active' => (Yii::$app->controller->id == 'product') &&
                            Yii::$app->controller->action->id === 'create',
                    ],

                ],
//                                'visible' => Yii::$app->user->can('accessProducts'),
                'visible' => !Yii::$app->user->can('streetAdmin') && !Yii::$app->user->can('streetSeller') && !Yii::$app->user->can('hotline'),
            ],
            [
                'label' => Yii::t('backend', 'Blocking'),
                'url' => [
                    '/Product/product/uiblock'
                ],

                'icon' =>  Icon::show('hand-spock', [ 'class'=>'nav-icon','framework'=> Icon::FAS]),
                'active' => (Yii::$app->controller->id == 'product') &&
                    Yii::$app->controller->action->id === 'uiblock',
                'visible' => !Yii::$app->user->can('streetAdmin') && !Yii::$app->user->can('streetSeller'),
            ],
            [
                'label' => Yii::t('backend', 'Time table'),
                'url' => [
                    '/Product/product/accesstimetable'
                ],
                'icon' =>  Icon::show('clipboard-list', [ 'class'=>'nav-icon','framework'=> Icon::FAS]),
                'active' => (Yii::$app->controller->id == 'product') &&
                    Yii::$app->controller->action->id === 'accesstimetable',
                'visible' => !Yii::$app->user->can('streetSeller')
            ],
            [
                'label' => Yii::t('backend', 'Payments'),
                'url' => [
                    '/Payment/payment/admin'
                ],
                'icon' =>  Icon::show('money-bill', [ 'class'=>'nav-icon','framework'=> Icon::FAS]),
                'active' => (Yii::$app->controller->id == 'payment'),
                'visible' => Yii::$app->user->can('accessPayments'),
            ],
            [
                'label' => Yii::t('backend', 'Bookings'),
                'options' => [
                 'class' => 'nav-item has-treeview'
                ],
                'url' => [
                    '/Reservations/reservations/admin'
                ],
                'options' => [
                 'class' => 'nav-item has-treeview'
                ],
                'icon' => Icon::show('chair') ,
                'active' => (Yii::$app->controller->id == 'reservations'),
                'items' => [
                    [
                        'label' => Yii::t('backend', 'Reporting'),
                        'icon' => '<i class="fa nav-icon fa-bar-chart-o"></i>',
                        'url' => [
                            '/Reservations/reservations/reporting'
                        ],
                        'badge' => TimelineEvent::find()->today()->count(),
                        'badgeBgClass' => 'label-success',
                        'visible'=>Yii::$app->user->can('administrator')or Yii::$app->user->can('streetAdmin')
                    ],
                    [
                        'label' => Yii::t('backend', 'Overview'),
                        'url' => [
                            '/Reservations/reservations/admin'
                        ],
                        'icon' => '<i class="fa nav-icon fa-list-alt"></i>',
                        'active' => (Yii::$app->controller->id == 'reservations') &&
                            Yii::$app->controller->action->id === 'admin',
                        'visible' => !Yii::$app->user->can('streetSeller'),
                    ],

                    [
                        'label' => Yii::t('backend', 'Create Booking'),
                        'url' => [
                            '/Reservations/reservations/create2'
                        ],
                        'icon' =>  Icon::show('buysellads', [ 'class'=>'nav-icon','framework'=> Icon::FAB]),
                        'active' => (Yii::$app->controller->id == 'reservations' &&
                            Yii::$app->controller->action->id === 'create2'),

                    ],
                    [
                        'label' => Yii::t('backend', 'Create (new)'),
                        'url' => [
                            '/Reservations/reservations/create-react'
                        ],
                        'icon' => '<i class="fa nav-icon fa-database"></i>',
                        'active' => (Yii::$app->controller->id == 'reservations' &&
                            Yii::$app->controller->action->id === 'create-react'),

                        'visible' => !Yii::$app->user->can('streetAdmin') && !Yii::$app->user->can('streetSeller') && !Yii::$app->user->can('hotline'),
                    ],
                    [
                        'label' => Yii::t('backend', 'myReservations'),
                        'url' => [
                            '/Reservations/reservations/myreservations'
                        ],
                        'icon' => '<i class="fa nav-icon fa-calendar"></i>',
                        'active' => (Yii::$app->controller->id == 'reservations' &&
                            Yii::$app->controller->action->id === 'myreservations'),
                    ],
                    [
                        'label' => Yii::t('backend', 'my Transactions'),
                        'url' => [
                            '/Reservations/reservations/mytransactions'
                        ],
                        'icon' => '<i class="fa nav-icon fa-dollar"></i>',
                        'active' => (Yii::$app->controller->id == 'reservations' &&
                            Yii::$app->controller->action->id === 'mytransactions'),
                    ],
                ],
                'visible' => Yii::$app->user->can('accessBookings'),
            ],
            [
                'label' => Yii::t('backend', 'Statistics'),
                'url' => [
                    '/Statistics/statistics/admin'
                ],
                'icon' =>  Icon::show('chart-line', [ 'class'=>'nav-icon','framework'=> Icon::FAS]),
                'active' => (Yii::$app->controller->id == 'statistics'),
                'visible' => Yii::$app->user->can('accessStatistics') && Yii::$app->user->can('viewStatisticsAdmin') && !Yii::$app->user->can('streetSeller') && !Yii::$app->user->can('streetAdmin') && !Yii::$app->user->can('streetSeller') && !Yii::$app->user->can('hotline'),
            ],

            [
                'label' => Yii::t('backend', 'Key-Value Storage'),
                'url' => [
                    '/system/key-storage/index'
                ],
                'icon' => '<i class="fa nav-icon fa-arrows-h"></i>',
                'active' => (Yii::$app->controller->id == 'key-storage'),
                'visible' => Yii::$app->user->can('accessKeyValueStorage'),
            ],
            [
                'label' => Yii::t('backend', 'Cache'),
                'url' => [
                    '/system/cache/index'
                ],
                'icon' => '<i class="fa nav-icon fa-refresh"></i>',
                'visible' => Yii::$app->user->can('accessSystemCache'),
            ],
            [
                'label' => Yii::t('backend', 'System Information'),
                'url' => [
                    '/system/information/index'
                ],
                'icon' => '<i class="fa nav-icon fa-dashboard"></i>',
                'visible' => Yii::$app->user->can('accessSystemInformation'),
            ],
            [
                'label' => Yii::t('backend', 'Logs'),
                'url' => [
                    '/system/log/index'
                ],
                'icon' => '<i class="fa nav-icon fa-warning"></i>',
                'badge' => SystemLog::find()->count(),
                'badgeBgClass' => 'label-danger',
                'visible' => Yii::$app->user->can('accessSystemLogs'),
            ],
        ],
    ];

?>

<?php $this->beginContent('@backend/views/layouts/base.php'); ?>


<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
            </li>

        </ul>

        <!-- SEARCH FORM -->


        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- Messages Dropdown Menu -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="fas fa-user-cog"></i>
                    <span class="badge badge-info navbar-badge"><?=Icon::show('level-down-alt')?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <a href="/sign-in/profile" class="dropdown-item">
                        <!-- Message Start -->
                        <div class="media">
                            <?=Icon::show('user-circle')?>
                            <div class="media-body">
                                <h3 class="dropdown-item-title">
                                    View Profile
                                </h3>
                            </div>
                        </div>
                        <!-- Message End -->
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="/sign-in/account" class="dropdown-item">
                        <!-- Message Start -->
                        <div class="media">
                            <?=Icon::show('cogs')?>   <div class="media-body">
                                <h3 class="dropdown-item-title">
                                    Account config
                                </h3>
                            </div>
                        </div>
                        <!-- Message End -->
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="/sign-in/logout" class="dropdown-item" data-method="post">
                        <!-- Message Start -->
                        <div class="media">
                            <?=Icon::show('sign-out-alt')?>
                            <div class="media-body">
                                <h3 class="dropdown-item-title">
                                    Log out
                                </h3>
                            </div>
                        </div>
                        <!-- Message End -->
                    </a>
                    <div class="dropdown-divider"></div>



                </div>

            </li>
            <!-- Notifications Dropdown Menu -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell"></i>
                    <span class="badge badge-warning navbar-badge">15</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-item dropdown-header">15 Notifications</span>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-envelope mr-2"></i> 4 new messages
                        <span class="float-right text-muted text-sm">3 mins</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-users mr-2"></i> 8 friend requests
                        <span class="float-right text-muted text-sm">12 hours</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-file mr-2"></i> 3 new reports
                        <span class="float-right text-muted text-sm">2 days</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#"><i
                            class="fas fa-th-large"></i></a>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="index3.html" class="brand-link">
            <!--img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
                 style="opacity: .8"-->
            <span class="brand-text font-weight-light">Madrapur</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <img src="<?php echo Yii::$app->user->identity->userProfile->getAvatar($this->assetManager->getAssetUrl($bundle, 'img/anonymous.jpg')) ?>" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" class="d-block">   <?php echo Yii::$app->user->identity->username ?></a>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
             <?=Menu::widget($sidemenu);?>
                <?php


                ?>



            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                            <?php echo $this->title ?>
                            <?php if (isset($this->params['subtitle'])): ?>
                                <small><?php echo $this->params['subtitle'] ?></small>
                            <?php endif; ?>
                        </h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">

                        <?php
                            echo Breadcrumbs::widget(
                                [
                                    'tag' => 'ol',
                                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                                ])?>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <?php if (Yii::$app->session->hasFlash('alert')): ?>
                    <?php

                    echo Alert::widget(
                        [
                            'body' => ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'body'),
                            'options' => ArrayHelper::getValue(Yii::$app->session->getFlash('alert'), 'options'),
                        ])?>
                <?php endif; ?>
                <?php echo $content ?>

            </div>

        </section>

        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->

    <!-- Main Footer -->
    <footer class="main-footer">
        <strong>Copyright &copy; 2014-2019 <a href="http://adminlte.io">AdminLTE.io</a>.</strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 3.0.0-rc.1
        </div>
    </footer>
</div>


<!-- ./wrapper -->

<?php $this->endContent(); ?>
