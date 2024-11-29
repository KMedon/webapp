<?php

namespace backend\library;

use backend\library\Model;
use backend\rdbms\RequestOperation;
use backend\rdbms\RequestResult;

use backend\rdbms\SqlQueryEngine;
use PDO;
use Exception;

use backend\rdbms\SimpleCondition;


class GenericModel extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    function select($entity, $idName, array $selectFields, array $requestFields): RequestResult
    {
        try {
            $pdo = $this->getPdoConnection();
            $idValue = $requestFields[$idName];

            $fieldsString = implode(', ', $selectFields);

            $query_string = "Select $fieldsString from $entity where $idName={$idValue}";  // space is necessary 
            $statement = $pdo->query($query_string, PDO::FETCH_ASSOC);

            //echo $query_string;
            //die;

            return RequestResult::requestSUCCESS(RequestOperation::SELECT, $pdo, $statement, $query_string);
        } catch (Exception $e) {
            throw $e;
            //return RequestResult::requestERROR(RequestOperation::SELECT, "error: " . $e->getMessage() . 'query='. $query_string);
        }
    }

    public function selectAll($entity, $idName, array $fields): RequestResult
    {
        try {
            $pdo = $this->getPdoConnection();
            $fieldsWithId = $fields;
            $fieldsWithId[] = $idName;
            $fieldsString = implode(', ', $fieldsWithId);
            $query_string = "Select $fieldsString from $entity WHERE (1=1)  ";  // space is necessary     
            $statement = $pdo->query($query_string, PDO::FETCH_ASSOC);

            return RequestResult::requestSUCCESS(RequestOperation::SELECT, $pdo, $statement, $query_string);
        } catch (Exception $e) {
            throw $e;
            //return RequestResult::requestERROR(RequestOperation::SELECT, "error: " . $e->getMessage());
        }
    }

    function selectAllfromParentKey($entity, $idParentName, $idParentValue, array $fields)
    {
        try {

            $result = SqlQueryEngine::factory()
                ->select($fields)
                ->from($entity)
                ->where((new SimpleCondition($idParentName, "=", $idParentValue)))
                ->execute();


            return $result;

            /*
              $pdo = $this->getPdoConnection();
              $fieldsWithId = $fields;
              $fieldsWithId[] = $idParentName;
              $fieldsString = implode(', ', $fieldsWithId);
              $query_string = "Select $fieldsString from $entity WHERE $idParentName='$idParentValue'";  // space is necessary
              $statement = $pdo->query($query_string, PDO::FETCH_ASSOC);

              return RequestResult::requestSUCCESS(RequestOperation::SELECT, $pdo, $statement, $query_string);
             */
        } catch (Exception $e) {
            throw $e;
            //return RequestResult::requestERROR(RequestOperation::SELECT, "error: " . $e->getMessage());
        }
    }


    // Model method
    public function listEntities(
        $entity,
        $idName,
        array $fields,
        $page,
        $rows,
        $sortField,
        $sortOrder,
        $filterObject,
        $custom_filters_callback = null
    ): RequestResult {
        try {
            $pdo = $this->getPdoConnection();
            $fieldsWithId = $fields;
            $fieldsWithId[] = $idName;
            $fieldsString = implode(', ', $fieldsWithId);

            // Start building the query
            $query_string = "SELECT $fieldsString FROM $entity WHERE (1=1)";

            // Add filters to the query
            $queryParams = [];
            foreach ($filterObject as $field => $criteria) {
                $value = $criteria['value'];
                $matchMode = $criteria['matchMode'];
                if ($value !== '') {
                    switch ($matchMode) {
                        case 'contains':
                            $query_string .= " AND $field LIKE :{$field}";
                            $queryParams[":{$field}"] = "%$value%";
                            break;
                        case 'equals':
                            $query_string .= " AND $field = :{$field}";
                            $queryParams[":{$field}"] = $value;
                            break;
                        case 'startsWith':
                            $query_string .= " AND $field LIKE :{$field}";
                            $queryParams[":{$field}"] = "$value%";
                            break;
                        case 'endsWith':
                            $query_string .= " AND $field LIKE :{$field}";
                            $queryParams[":{$field}"] = "%$value";
                            break;
                        case '>':
                            $query_string .= " AND $field > :{$field}";
                            $queryParams[":{$field}"] = $value;
                            break;
                        case '<':
                            $query_string .= " AND $field < :{$field}";
                            $queryParams[":{$field}"] = $value;
                            break;
                        case '>=':
                            $query_string .= " AND $field >= :{$field}";
                            $queryParams[":{$field}"] = $value;
                            break;
                        case '<=':
                            $query_string .= " AND $field <= :{$field}";
                            $queryParams[":{$field}"] = $value;
                            break;
                    }
                }
            }
            if(isset($custom_filters_callback)){
                $custom_filters_callback($query_string, $queryParams);
            }

            // Add sorting to the query
            if ($sortField) {
                $order = $sortOrder == 1 ? 'ASC' : 'DESC';
                $query_string .= " ORDER BY $sortField $order";
            }

            // Add pagination to the query
            $offset = $page * $rows;
            $query_string .= " LIMIT :limit OFFSET :offset";


            // Prepare and execute the main query
            $statement = $pdo->prepare($query_string);
            foreach ($queryParams as $param => $value) {
                $statement->bindValue($param, $value);
            }
            $statement->bindParam(':limit', $rows, PDO::PARAM_INT);
            $statement->bindParam(':offset', $offset, PDO::PARAM_INT);

            //echo($query_string);
            //die;
            $statement->execute();

            $jsonRequestResult = RequestResult::requestSUCCESS(RequestOperation::SELECT, $pdo, $statement, $query_string);

            // Build the count query to get the total number of records
            $count_query_string = "SELECT COUNT(*) as total FROM $entity WHERE (1=1)";
            foreach ($filterObject as $field => $criteria) {
                $value = $criteria['value'];
                $matchMode = $criteria['matchMode'];
                if ($value !== '') {
                    switch ($matchMode) {
                        case 'contains':
                            $count_query_string .= " AND $field LIKE :{$field}";
                            break;
                        case 'equals':
                            $count_query_string .= " AND $field = :{$field}";
                            break;
                        case 'startsWith':
                            $count_query_string .= " AND $field LIKE :{$field}";
                            break;
                        case 'endsWith':
                            $count_query_string .= " AND $field LIKE :{$field}";
                            break;
                        case '>':
                            $count_query_string .= " AND $field > :{$field}";
                            break;
                        case '<':
                            $count_query_string .= " AND $field < :{$field}";
                            break;
                        case '>=':
                            $count_query_string .= " AND $field >= :{$field}";
                            break;
                        case '<=':
                            $count_query_string .= " AND $field <= :{$field}";
                            break;
                    }
                }
            }

            // Prepare and execute the count query
            $count_statement = $pdo->prepare($count_query_string);
            foreach ($queryParams as $param => $value) {
                $count_statement->bindValue($param, $value);
            }
            $count_statement->execute();
            $count_result = $count_statement->fetch(PDO::FETCH_ASSOC);

            // Add the total record count to the result
            $jsonRequestResult->rowCount = $count_result['total'];

            return $jsonRequestResult;
        } catch (Exception $e) {
            throw $e;
            //return RequestResult::requestERROR(RequestOperation::SELECT, "error: " . $e->getMessage());
        }
    }



    public function listEntitiesWithJoins(
        $entity,
        $joins,
        $idName,
        array $fields,
        $page,
        $rows,
        $sortField,
        $sortOrder,
        $globalFilter,
        $filterObject,        
        $custom_filters_callback = null
    ): RequestResult {
        try {
            //echo json_encode($globalFilter);
            //die;

            $pdo = $this->getPdoConnection();
            $fieldsWithId = $fields;
            $fieldsWithId[] = $idName;
            $fieldsString = implode(', ', $fieldsWithId);

            // Start building the query
            $query_string = "SELECT $fieldsString FROM $entity $joins";
            
            $where_clause = " WHERE (1=1) ";

            $global_search_where_clause = "";

            // Add filters to the query
            $queryParams = [];
            foreach ($filterObject as $field => $criteria) {
                $value = $criteria['value'];
                $matchMode = $criteria['matchMode'];

                //$field_2 = $field;
                //if($field=="is_new") {
                    $field_2 = "$entity.$field";
                //}

                //global filter:
                if(isset($globalFilter)) {
                    $global_search_where_clause .= " OR $field_2 LIKE :global_$field";
                    $globalFilterValue = $globalFilter["value"];
                    $queryParams[":global_$field"] = "%$globalFilterValue%";
                }

                if ($value !== '') {
                    switch ($matchMode) {
                        case 'contains':
                            $where_clause .= " AND $field_2 LIKE :$field";
                            $queryParams[":$field"] = "%$value%";                                                        
                            break;
                        case 'equals':
                            $where_clause .= " AND $field_2 = :$field";
                            $queryParams[":$field"] = $value;
                            break;
                        case 'startsWith':
                            $where_clause .= " AND $field_2 LIKE :$field";
                            $queryParams[":$field"] = "$value%";
                            break;
                        case 'endsWith':
                            $where_clause .= " AND $field_2 LIKE :$field";
                            $queryParams[":$field"] = "%$value";
                            break;
                        case '>':
                            $where_clause .= " AND $field_2 > :$field";
                            $queryParams[":$field"] = $value;
                            break;
                        case '<':
                            $where_clause .= " AND $field_2 < :$field";
                            $queryParams[":$field"] = $value;
                            break;
                        case '>=':
                            $where_clause .= " AND $field_2 >= :$field";
                            $queryParams[":$field"] = $value;
                            break;
                        case '<=':
                            $where_clause .= " AND $field_2 <= :$field";
                            $queryParams[":$field"] = $value;
                            break;
                    }
                }
            }
            if(isset($custom_filters_callback)){
                $custom_filters_callback($where_clause);
            }
            
            $query_string .=  $where_clause;
            
            if($global_search_where_clause!=""){ 
                $query_string .= " and (false " . $global_search_where_clause . ")";                
            }
           
            
            // Add sorting to the query
            if ($sortField) {
                $order = $sortOrder == 1 ? 'ASC' : 'DESC';
                $query_string .= " ORDER BY $sortField $order";
            }

            // Add pagination to the query
            $offset = $page * $rows;
            $query_string .= " LIMIT :limit OFFSET :offset";


            // Prepare and execute the main query
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            $statement = $pdo->prepare($query_string);
            foreach ($queryParams as $param => $value) {
                $statement->bindValue($param, $value);
            }
            $statement->bindParam(':limit', $rows, PDO::PARAM_INT);
            $statement->bindParam(':offset', $offset, PDO::PARAM_INT);

            //echo($query_string);
            //echo json_encode($queryParams);
            //die;
            $statement->execute();  //chatGPT:this is line 356

            $jsonRequestResult = RequestResult::requestSUCCESS(RequestOperation::SELECT, $pdo, $statement, $query_string);

            // Build the count query to get the total number of records
            $count_query_string = "SELECT COUNT(*) as total FROM $entity " . $where_clause;

            if($global_search_where_clause!=""){ 
                $count_query_string .= " and (false " . $global_search_where_clause . ")";                
            }

            
            // Prepare and execute the count query
            $count_statement = $pdo->prepare($count_query_string);
            foreach ($queryParams as $param => $value) {
                $count_statement->bindValue($param, $value);
            }
            $count_statement->execute();  //chatGPT: this is line 404
            $count_result = $count_statement->fetch(PDO::FETCH_ASSOC);

            // Add the total record count to the result
            $jsonRequestResult->rowCount = $count_result['total'];

            return $jsonRequestResult;
        } catch (Exception $e) {
            throw $e;
            //return RequestResult::requestERROR(RequestOperation::SELECT, "error: " . $e->getMessage());
        }
    }






    // $dataArray = ["id" =>1 , "name" => "Jonh", "date" : Date() ];
    function insert($entity, array $insertFields, array $dataArray): RequestResult
    {
        try {

            $pdo = $this->getPdoConnection();

            $fieldsString = implode(', ', $insertFields);

            $handlersString = implode(', ', array_map(function ($key) {
                return ":$key";
            }, $insertFields));

            /* $dataValues = implode(', ', array_map(function ($key) use ($dataArray) {
              return $dataArray[$key];
              }, $insertFields)); */
            // prepare and bind
            $query_string = "INSERT INTO $entity ($fieldsString) VALUES ($handlersString)";
            $statement = $pdo->prepare($query_string);
            foreach ($insertFields as $field) {
                $statement->bindParam(":" . $field, $dataArray[$field], PDO::PARAM_STR);
            }

            $statement->execute();

            return RequestResult::requestSUCCESS(RequestOperation::INSERT, $pdo, $statement, $query_string);
        } catch (Exception $e) {
            throw $e;
            //return RequestResult::requestERROR(RequestOperation::INSERT, "error inserting a $entity: " . $e->getMessage() );            
        }
    }

    // $updateFields = [name, date, ...] tells which fields to save
    // $requestData = ["id" =>1 , "name" => "Jonh", "date" : Date() ];
    function update($entity, $idName, array $updateFields, $requestData)
    {
        try {
            /*
              if( $idValue !== $requestData[$idName] ) {
              $aux = $requestData[$idName];
              throw new Exception("The ID of the entity was not properly specified: $idValue and $aux");
              }
             */
            //echo json_encode($requestData); 

            $idValue = $requestData[$idName];  // this is line 434
          
            $fieldsValuesHandlers = implode(', ', array_map(function ($field) {
                return "$field = :$field";
            }, $updateFields));

            $dataArray = array_values($requestData);

            if (strlen($fieldsValuesHandlers) == 0) {
                throw new Exception("No fields to update were specified");
            }

            $query_string = "update $entity set " . $fieldsValuesHandlers . " where $idName = {$idValue}";

            //echo $query_string; die;

            $pdo = $this->getPdoConnection();
            $statement = $pdo->prepare($query_string);

            foreach ($updateFields as $field) {
                $statement->bindParam(":" . $field, $requestData[$field], PDO::PARAM_STR);
            }

            //echo $query_string;
            //die;

            $statement->execute();

            $requestResult = RequestResult::requestSUCCESS(RequestOperation::UPDATE, $pdo, $statement, "$entity updated with success");
            $requestResult->id = $idValue;
            $requestResult->idName = $idName;
            $requestResult->idValue = $idValue;
            return $requestResult;
        } catch (Exception $e) {
            //echo $e->getMessage();
            throw $e;
            //return RequestResult::requestERROR(RequestOperation::UPDATE, "error updating $entity: " . $e->getMessage() );            
        }
    }

    function delete($entity, $idName, $requestData)
    {
        try {

            $idValue = $requestData[$idName];

            if (!isset($idValue)) {
                throw new Exception("The ID of the $entity was not specified");
            }


            $query_string = "delete from $entity where $idName = {$idValue}";

            $pdo = $this->getPdoConnection();
            $statement = $pdo->prepare($query_string);
            $statement->execute(/* [  $idName => $idValue  ]  */);
            $requestResult = RequestResult::requestSUCCESS(RequestOperation::DELETE, $pdo, $statement, "$entity deleted with success");
            $requestResult->id = $idValue;
            $requestResult->idName = $idName;
            $requestResult->idValue = $idValue;
            return $requestResult;
        } catch (Exception $e) {
            //echo $e->getMessage();
            throw $e;
            //return RequestResult::requestERROR(RequestOperation::DELETE, "error deleting $entity: " . $e->getMessage() . " query = " . $query_string );            
        }
    }

    function queryWithCondition($queryHandler): RequestResult
    {

        //echo $queryString;
        //die;
        try {
            $queryString = $queryHandler();
            $pdo = $this->getPdoConnection();
            $statement = $pdo->prepare($queryString);
            $statement->execute(/* [  $idName => $idValue  ]  */);
            $requestResult = RequestResult::requestSUCCESS(RequestOperation::QUERY, $pdo, $statement, "executed with success");
            return $requestResult;
        } catch (Exception $e) {
            //echo $e->getMessage();
            throw $e;
            //return RequestResult::requestERROR(RequestOperation::DELETE, "error in query: " . $e->getMessage() . " query = " . $queryString );            
        }
    }

    
}
