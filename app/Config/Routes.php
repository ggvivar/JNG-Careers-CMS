<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);

$routes->get('/', 'AuthController::loginForm');
$routes->get('/forgot-password', 'AuthController::forgotPasswordForm');
$routes->post('/login', 'AuthController::login');
$routes->post('/forgot-password', 'AuthController::forgot_password');
$routes->get('logout', 'AuthController::logout');

$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
    $routes->get('login', 'AuthController::loginForm');
    $routes->post('login', 'AuthController::login');
    $routes->get('logout', 'AuthController::logout');

    $routes->group('', ['filter' => 'adminauth'], function ($routes) {
        $routes->get('/', 'DashboardController::index');

        $routes->get('users', 'UserController::index', ['filter' => 'adminfeature:users,can_view']);
        $routes->match(['get', 'post'], 'users/create', 'UserController::create', ['filter' => 'adminfeature:users,can_add']);
        $routes->match(['get', 'post'], 'users/edit/(:num)', 'UserController::edit/$1', ['filter' => 'adminfeature:users,can_edit']);
        $routes->post('users/delete/(:num)', 'UserController::delete/$1', ['filter' => 'adminfeature:users,can_delete']);

        $routes->get('roles', 'RoleController::index', ['filter' => 'adminfeature:roles,can_view']);
        $routes->match(['get', 'post'], 'roles/create', 'RoleController::create', ['filter' => 'adminfeature:roles,can_add']);
        $routes->match(['get', 'post'], 'roles/edit/(:num)', 'RoleController::edit/$1', ['filter' => 'adminfeature:roles,can_edit']);
        $routes->post('roles/delete/(:num)', 'RoleController::delete/$1', ['filter' => 'adminfeature:roles,can_delete']);
        $routes->match(['get', 'post'], 'roles/(:num)/features', 'RoleController::features/$1', ['filter' => 'adminfeature:roles,can_edit']);
        $routes->match(['get', 'post'], 'roles/(:num)/modules', 'RoleController::features/$1', ['filter' => 'adminfeature:roles,can_edit']);
        $routes->match(['get', 'post'], 'roles/(:num)/users', 'RoleController::users/$1', ['filter' => 'adminfeature:roles,can_edit']);

        $routes->get('modules', 'ModuleController::index', ['filter' => 'adminfeature:modules,can_view']);
        $routes->match(['get', 'post'], 'modules/create', 'ModuleController::create', ['filter' => 'adminfeature:modules,can_add']);
        $routes->match(['get', 'post'], 'modules/edit/(:num)', 'ModuleController::edit/$1', ['filter' => 'adminfeature:modules,can_edit']);
        $routes->post('modules/delete/(:num)', 'ModuleController::delete/$1', ['filter' => 'adminfeature:modules,can_delete']);

        $routes->get('categories', 'CategoryController::index', ['filter' => 'adminfeature:categories,can_view']);
        $routes->match(['get', 'post'], 'categories/create', 'CategoryController::create', ['filter' => 'adminfeature:categories,can_add']);
        $routes->match(['get', 'post'], 'categories/edit/(:num)', 'CategoryController::edit/$1', ['filter' => 'adminfeature:categories,can_edit']);
        $routes->post('categories/delete/(:num)', 'CategoryController::delete/$1', ['filter' => 'adminfeature:categories,can_delete']);

        $routes->get('status', 'StatusController::index', ['filter' => 'adminfeature:status,can_view']);
        $routes->match(['get', 'post'], 'status/create', 'StatusController::create', ['filter' => 'adminfeature:status,can_add']);
        $routes->match(['get', 'post'], 'status/edit/(:num)', 'StatusController::edit/$1', ['filter' => 'adminfeature:status,can_edit']);
        $routes->post('status/delete/(:num)', 'StatusController::delete/$1', ['filter' => 'adminfeature:status,can_delete']);

        $routes->get('companies', 'CompanyController::index', ['filter' => 'adminfeature:companies,can_view']);
        $routes->match(['get', 'post'], 'companies/create', 'CompanyController::create', ['filter' => 'adminfeature:companies,can_add']);
        $routes->match(['get', 'post'], 'companies/edit/(:num)', 'CompanyController::edit/$1', ['filter' => 'adminfeature:companies,can_edit']);
        $routes->post('companies/delete/(:num)', 'CompanyController::delete/$1', ['filter' => 'adminfeature:companies,can_delete']);

        $routes->get('departments', 'DepartmentController::index', ['filter' => 'adminfeature:departments,can_view']);
        $routes->match(['get', 'post'], 'departments/create', 'DepartmentController::create', ['filter' => 'adminfeature:departments,can_add']);
        $routes->match(['get', 'post'], 'departments/edit/(:num)', 'DepartmentController::edit/$1', ['filter' => 'adminfeature:departments,can_edit']);
        $routes->post('departments/delete/(:num)', 'DepartmentController::delete/$1', ['filter' => 'adminfeature:departments,can_delete']);

        $routes->get('jobs', 'JobController::index', ['filter' => 'adminfeature:jobs,can_view']);
        $routes->match(['get', 'post'], 'jobs/create', 'JobController::create', ['filter' => 'adminfeature:jobs,can_add']);
        $routes->match(['get', 'post'], 'jobs/edit/(:num)', 'JobController::edit/$1', ['filter' => 'adminfeature:jobs,can_edit']);
        $routes->post('jobs/delete/(:num)', 'JobController::delete/$1', ['filter' => 'adminfeature:jobs,can_delete']);

        $routes->get('job-posts', 'JobPostController::index', ['filter' => 'adminfeature:job-posts,can_view']);
        $routes->match(['get', 'post'], 'job-posts/create', 'JobPostController::create', ['filter' => 'adminfeature:job-posts,can_add']);
        $routes->match(['get', 'post'], 'job-posts/edit/(:num)', 'JobPostController::edit/$1', ['filter' => 'adminfeature:job-posts,can_edit']);
        $routes->post('job-posts/delete/(:num)', 'JobPostController::delete/$1', ['filter' => 'adminfeature:job-posts,can_delete']);

        $routes->get('applicants', 'ApplicantController::index', ['filter' => 'adminfeature:applicants,can_view']);
        $routes->get('applicants/(:num)', 'ApplicantController::view/$1', ['filter' => 'adminfeature:applicants,can_view']);

        $routes->get('applications', 'ApplicationController::index', ['filter' => 'adminfeature:applications,can_view']);
        $routes->get('applications/(:num)', 'ApplicationController::view/$1', ['filter' => 'adminfeature:applications,can_view']);
        $routes->post('applications/(:num)/status', 'ApplicationController::updateStatus/$1', ['filter' => 'adminfeature:applications,can_edit']);

        $routes->get('contents', 'ContentController::index', ['filter' => 'adminfeature:contents,can_view']);
        $routes->match(['get', 'post'], 'contents/create', 'ContentController::create', ['filter' => 'adminfeature:contents,can_add']);
        $routes->match(['get', 'post'], 'contents/edit/(:num)', 'ContentController::edit/$1', ['filter' => 'adminfeature:contents,can_edit']);
        $routes->post('contents/delete/(:num)', 'ContentController::delete/$1', ['filter' => 'adminfeature:contents,can_delete']);
        $routes->post('contents/(:num)/submit', 'ContentController::submit/$1', ['filter' => 'adminfeature:contents,can_edit']);
        $routes->post('contents/(:num)/approve', 'ContentController::approve/$1', ['filter' => 'adminfeature:contents,can_edit']);
        $routes->post('contents/(:num)/reject', 'ContentController::reject/$1', ['filter' => 'adminfeature:contents,can_edit']);
        $routes->get('contents/(:num)', 'ContentController::view/$1', ['filter' => 'adminfeature:contents,can_view']);
        
        $routes->get('export/(:segment)', 'ImportExportController::export/$1');
        $routes->post('import/(:segment)', 'ImportExportController::import/$1');
        //Feature 
        $routes->get('features', 'FeatureController::index', ['filter' => 'adminfeature:modules,can_view']);
        $routes->match(['get', 'post'], 'features/create', 'FeatureController::create', ['filter' => 'adminfeature:modules,can_add']);
        $routes->match(['get', 'post'], 'features/edit/(:num)', 'FeatureController::edit/$1', ['filter' => 'adminfeature:modules,can_edit']);
        $routes->post('features/delete/(:num)', 'FeatureController::delete/$1', ['filter' => 'adminfeature:modules,can_delete']);
        //Site Settings
        $routes->get('site-settings', 'SiteSettingsController::index', ['filter' => 'adminfeature:site-settings,can_view']);
        $routes->post('site-settings/save', 'SiteSettingsController::save', ['filter' => 'adminfeature:site-settings,can_edit']);
        });
        //Messaging 
        $routes->get('message-templates', 'MessageTemplateController::index', ['filter' => 'adminfeature:message-templates,can_view']);
        $routes->match(['get', 'post'], 'message-templates/create', 'MessageTemplateController::create', ['filter' => 'adminfeature:message-templates,can_add']);
        $routes->match(['get', 'post'], 'message-templates/edit/(:num)', 'MessageTemplateController::edit/$1', ['filter' => 'adminfeature:message-templates,can_edit']);
        $routes->post('message-templates/delete/(:num)', 'MessageTemplateController::delete/$1', ['filter' => 'adminfeature:message-templates,can_delete']);

        $routes->get('document-templates', 'DocumentTemplateController::index', ['filter' => 'adminfeature:document-templates,can_view']);
        $routes->match(['get', 'post'], 'document-templates/create', 'DocumentTemplateController::create', ['filter' => 'adminfeature:document-templates,can_add']);
        $routes->match(['get', 'post'], 'document-templates/edit/(:num)', 'DocumentTemplateController::edit/$1', ['filter' => 'adminfeature:document-templates,can_edit']);
        $routes->post('document-templates/delete/(:num)', 'DocumentTemplateController::delete/$1', ['filter' => 'adminfeature:document-templates,can_delete']);
        });
//APIs Routes
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    // var_dump('here');
    // die();
$routes->get('send', 'NotificationApiController::send');
$routes->get('send_attachment', 'NotificationApiController::send_attachment');
    //Contents
    $routes->get('content/all', 'ContentController::all');
    //Content-Category
    
    $routes->get('content/(:segment)/(:segment)/(:segment)/(:segment)', 'ContentController::byCategoryKeys/$1/$2/$3/$4');
    $routes->get('content/category', 'ContentController::byCategory');
    $routes->get('content/category/(:segment)', 'ContentController::byCategory/$1');


    $routes->get('content/year/(:segment)', 'ContentController::year/$1');
     // applicant auth
    $routes->post('applicant/register', 'ApplicantController::register');
    $routes->post('applicant/login', 'ApplicantController::login');
    $routes->post('applicant/forgot-password', 'ApplicantController::forgotPassword');
    $routes->post('applicant/reset-password', 'ApplicantController::resetPassword');

    // public jobs
    $routes->get('jobs', 'JobPortalController::jobs');
    $routes->get('jobs/(:num)', 'JobPortalController::jobDetail/$1');

    // protected applicant portal
    $routes->group('', ['filter' => 'applicantauth'], function ($routes) {
        $routes->get('applicant/me', 'ApplicantController::me');
        $routes->post('applicant/edit', 'ApplicantController::edit');
        $routes->post('applicant/logout', 'ApplicantController::logout');
        $routes->get('applicant/dashboard', 'ApplicantController::dashboard');
        $routes->post('applicant/upload-resume', 'ApplicantController::uploadResume');

        $routes->get('application/mine', 'ApplicationController::mine');
        $routes->get('application/(:num)', 'ApplicationController::detail/$1');
        $routes->post('application/create', 'ApplicationController::create');
        $routes->post('application/edit/(:num)', 'ApplicationController::edit/$1');
        $routes->post('application/withdraw/(:num)', 'ApplicationController::withdraw/$1');

        $routes->get('notifications', 'NotificationController::index');
        $routes->post('notifications/read/(:num)', 'NotificationController::markRead/$1');
    });
    
});


