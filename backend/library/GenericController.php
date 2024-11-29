<?php

namespace backend\library;

use \backend\library\Controller;



use  \backend\library\GenericModel;


class GenericController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public static function entityName()
    {
        return "";
    }
    public static function identityField()
    {
        return "id";
    }
    public static function parentIdentityField()
    {
        return "parent_id";
    } // it is the foreign key of the parent entity

    public static function crudFields($mode, $format)
    {
        return array();
    }


    function select($requestFields)
    {
        // we could define functions like selectPersonCity, selectPerson
        $genericModel = new GenericModel();
        $genericModel->select(static::entityName(), static::identityField(), static::crudFields(Controller::SEE, "array"), $requestFields)->toJsonEcho();
    }


    function selectAllFromParentKey($requestFields)
    {
        // we could define functions like selectPersonCity, selectPerson
        $genericModel = new GenericModel();



        $parentIDValuve = $requestFields[static::parentIdentityField()];

        $genericModel->selectAllFromParentKey(
            static::entityName(),
            static::parentIdentityField(),
            $parentIDValuve,
            static::crudFields(Controller::SEE, "array"),
            $requestFields
        )->toJsonEcho();
    }


    // http://localhost/app.php?service=selectPeople
    function selectAll()
    {
        $personModel = new GenericModel();
        $personModel->selectAll(static::entityName(), static::identityField(), static::crudFields(Controller::SEE, "array"))->toJsonEcho();
    }

    // Controller method
    // Controller method
    function listEntities()
    {
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 0;
        $rows = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10;  // Default rows to 10 if not specified
        $sortField = isset($_REQUEST['sortField']) ? $_REQUEST['sortField'] : '';
        $sortOrder = isset($_REQUEST['sortOrder']) ? $_REQUEST['sortOrder'] : 1; // Default to ascending order

        // Assuming the filters parameter is sent as a query string
        $filters = isset($_REQUEST['filters']) ? json_decode($_REQUEST['filters'], true) : [];

        // Process the filters accordingly
        $filterObject = array();
        foreach ($filters as $field => $criteria) {
            $value = $criteria['value'];
            $matchMode = $criteria['matchMode'];
            // Add your filtering logic here based on $field, $value, and $matchMode
            $filterObject[$field] = array();
            $filterObject[$field]['value'] = $value;
            $filterObject[$field]['matchMode'] = $matchMode;
        }
        $model = new GenericModel();
        $model->listEntities(
            static::entityName(),
            static::identityField(),
            static::crudFields(Controller::SEE, "array"),
            $page,
            $rows,
            $sortField,
            $sortOrder,
            $filterObject
        )->toJsonEcho();
    }



    function showCrudTable($viewPath)
    {
        include($viewPath);
    }



    function insert($requestData)
    {
        /*
        $requestData = array(
            "name"       => @$_REQUEST["name"],
            "address"    => @$_REQUEST["address"],
            "city"       => @$_REQUEST["city"],
            "postalcode" => @$_REQUEST["postalcode"] 
        );
        */

        //error_log(json_encode($requestData));
        $genericModel = new GenericModel();
        $genericModel->insert(static::entityName(), static::crudFields(Controller::INSERT, "array"), $requestData)->toJsonEcho();
    }


    

    function update($requestData)
    {
        /*
        $requestData = array(
            "id"         => @$_REQUEST["id"],
            "name"       => @$_REQUEST["name"],
            "address"    => @$_REQUEST["address"],
            "city"       => @$_REQUEST["city"],
            "postalcode" => @$_REQUEST["postalcode"] 
        );
        */
        

        $genericModel = new GenericModel();
        $genericModel->update(static::entityName(), static::identityField(), static::crudFields(Controller::UPDATE, "array"),  $requestData)->toJsonEcho();
    }

    function delete($requesData)
    {

        $genericModel = new GenericModel();
        $genericModel->delete(static::entityName(), static::identityField(), $requesData)->toJsonEcho();
    }
}
