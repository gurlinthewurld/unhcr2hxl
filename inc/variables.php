<?php

/*
 * Log files
 */
$logETL = '../logs/unhcr2hxl_etl.log';
$logCreator = '../logs/unhcr2hxl_creators.log';
$logDelete = '../logs/unhcr2hxl_delete.log';
$logFile = '../logs/unhcr2hxl_default.log';
//$scriptCurlDrop = '../logs/unhcr2hxl_curlDropScript.txt';

/*
 * Configuration
 */
$sourceConfigFile = '../ini/unhcr2hxl_etl_source.ini';
$storeConfigFile = '../ini/unhcr2hxl_etl_store.ini';
$storeConfig = parse_ini_file($storeConfigFile); 
$sourceConfig = parse_ini_file($sourceConfigFile); 

/*
 * Container data
 */
$currentEmergency = 'http://hxl.humanitarianresponse.info/data/emergencies/mali2012test';
$timeStampLocationContainer = "1234567890.111111";
$reporter = 'vincent_perrin';
$reporterOrganisationAbbr = 'unocha';
$defaultPopulationType = 'refugeesasylumseekers';

/*
 * MySQL
 */
$mysqlQueryCountRows = "SELECT COUNT(*) FROM unhcr2hxl_datarefpop ";

$mysqlQuerySources = "SELECT * FROM unhcr2hxl_sourcetranslation";

$mysqlQueryLocations = "SELECT easyname
FROM unhcr2hxl_settlementpcode";

$mysqlQueryManyRows = "SELECT DISTINCT unhcr2hxl_datarefpop.ReportDate, unhcr2hxl_datarefpop.UpdatedDate, 
unhcr2hxl_datarefpop.DataSource AS source, unhcr2hxl_settlementpcode.aplpcode AS aplpcode, unhcr2hxl_settlementpcode.easyname AS easyname, unhcr2hxl_settlementpcode.pplpcode AS pplpcode, unhcr2hxl_settlementpcode.defaultlocationpcode AS defaultlocationpcode,
unhcr2hxl_settlementpcode.countrycode AS countrycode, unhcr2hxl_datarefpop.origin, 
unhcr2hxl_datarefpop.TotalRefPop_HH, unhcr2hxl_datarefpop.TotalRefPop_I, unhcr2hxl_datarefpop.DEM_04_M,
unhcr2hxl_datarefpop.DEM_04_F, unhcr2hxl_datarefpop.DEM_511_M, unhcr2hxl_datarefpop.DEM_511_F, 
unhcr2hxl_datarefpop.DEM_1217_M, unhcr2hxl_datarefpop.DEM_1217_F, unhcr2hxl_datarefpop.DEM_1859_M,
unhcr2hxl_datarefpop.DEM_1859_F, unhcr2hxl_datarefpop.DEM_60_M, unhcr2hxl_datarefpop.DEM_60_F 
FROM unhcr2hxl_datarefpop 
LEFT JOIN unhcr2hxl_settlement ON unhcr2hxl_datarefpop.Settlement = unhcr2hxl_settlement.Id 
LEFT JOIN unhcr2hxl_settlementpcode ON unhcr2hxl_settlement.Id = unhcr2hxl_settlementpcode.Id 
LEFT JOIN unhcr2hxl_countrypcode ON unhcr2hxl_settlement.Country = unhcr2hxl_countrypcode.Id 
WHERE (unhcr2hxl_settlement.SettlementName IS NOT NULL) 
ORDER BY ReportDate DESC 
LIMIT 20";// Set this value for your tests

$mysqlQueryAllRows = "SELECT DISTINCT unhcr2hxl_datarefpop.ReportDate, unhcr2hxl_datarefpop.UpdatedDate, 
unhcr2hxl_datarefpop.DataSource AS source, unhcr2hxl_settlementpcode.aplpcode AS aplpcode, unhcr2hxl_settlementpcode.easyname AS easyname, unhcr2hxl_settlementpcode.pplpcode AS pplpcode, unhcr2hxl_settlementpcode.defaultlocationpcode AS defaultlocationpcode,
unhcr2hxl_settlementpcode.countrycode AS countrycode, unhcr2hxl_datarefpop.origin, 
unhcr2hxl_datarefpop.TotalRefPop_HH, unhcr2hxl_datarefpop.TotalRefPop_I, unhcr2hxl_datarefpop.DEM_04_M,
unhcr2hxl_datarefpop.DEM_04_F, unhcr2hxl_datarefpop.DEM_511_M, unhcr2hxl_datarefpop.DEM_511_F, 
unhcr2hxl_datarefpop.DEM_1217_M, unhcr2hxl_datarefpop.DEM_1217_F, unhcr2hxl_datarefpop.DEM_1859_M,
unhcr2hxl_datarefpop.DEM_1859_F, unhcr2hxl_datarefpop.DEM_60_M, unhcr2hxl_datarefpop.DEM_60_F 
FROM unhcr2hxl_datarefpop 
LEFT JOIN unhcr2hxl_settlement ON unhcr2hxl_datarefpop.Settlement = unhcr2hxl_settlement.Id 
LEFT JOIN unhcr2hxl_settlementpcode ON unhcr2hxl_settlement.Id = unhcr2hxl_settlementpcode.Id 
LEFT JOIN unhcr2hxl_countrypcode ON unhcr2hxl_settlement.Country = unhcr2hxl_countrypcode.Id 
WHERE (unhcr2hxl_settlement.SettlementName IS NOT NULL) 
ORDER BY ReportDate";//

$mysqlQueryPcodes = "SELECT *
FROM unhcr2hxl_settlementpcode";

/*
 * SPARQL
 */
$ttlPrefixes = "prefix xsd: <http://www.w3.org/2001/XMLSchema#>
prefix dct: <http://purl.org/dc/terms/>  
prefix dc: <http://purl.org/dc/elements/1.1/> 
prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> 
prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#> 
prefix owl: <http://www.w3.org/2002/07/owl#>
prefix skos: <http://www.w3.org/2004/02/skos/core#>
prefix foaf: <http://xmlns.com/foaf/0.1/>  
prefix hxl: <http://hxl.humanitarianresponse.info/ns/#>
prefix geo: <http://www.opengis.net/ont/OGC-GeoSPARQL/1.0/>"; 

$ttlContainerUri = "http://hxl.humanitarianresponse.info/data/datacontainers/[%timeStamp%]";
$ttlContainerHeader = "<[%containerUri%]> a hxl:DataContainer . 
<[%containerUri%]> hxl:aboutEmergency <[%currentEmergency%]> . 
<[%containerUri%]> dc:date \"[%reportDate%]\"^^xsd:date . 
<[%containerUri%]> hxl:validOn \"[%validOn%]\" . 
<[%containerUri%]> hxl:reportCategory <http://hxl.humanitarianresponse.info/data/reportcategories/humanitarian_profile> . 
<[%containerUri%]> hxl:reportedBy <http://hxl.humanitarianresponse.info/data/persons/[%reporterOrg%]/[%reporter%]> . ";
//echo $ttlContainerHeader;

$ttlContainerHeaderLocations = "<[%containerUri%]> a hxl:DataContainer . 
<[%containerUri%]> dc:date \"[%reportDate%]\"^^xsd:date . ";

$ttlSubject = "<http://hxl.humanitarianresponse.info/data/[%populationType%]/[%countryPCode%]/[%campPCode%]/mli/[%originPCode%]/[%sex%]/[%age%]>"; // he pattern is : http://hxl.humanitarianresponse.info/data/populations/country/pcode/nationality/origin/sex/agegroup  
$ttlSex = "[%subject%] hxl:sexCategory <http://hxl.humanitarianresponse.info/data/sexcategories/[%sex%]> . ";
$ttlAge = "[%subject%] hxl:AgeGroup <http://hxl.humanitarianresponse.info/data/agegroups/unhcr/[%age%]> . ";
$ttlPersonCount = "[%subject%] hxl:personCount \"[%personCount%]\"^^xsd:integer . ";
$ttlHouseholdCount = "[%subject%] hxl:householdCount \"[%householdCount%]\"^^xsd:integer . ";
$ttlMethod = "[%subject%] hxl:method \"[%method%]\" . ";
$ttlSource = "[%subject%] hxl:source <http://hxl.humanitarianresponse.info/data/organisations/[%source%]> . ";

$ttlPopDescription = "[%subject%] hxl:atLocation <http://hxl.humanitarianresponse.info/data/locations/[%placeType%]/[%countryPCode%]/[%campPCode%]> . 
[%subject%] rdf:type hxl:RefugeesAsylumSeekers .
[%ttlSex%]
[%ttlAge%]
[%subject%] hxl:placeOfOrigin <http://hxl.humanitarianresponse.info/data/locations/admin/mli/MLI> . 
[%subject%] hxl:nationality <http://hxl.humanitarianresponse.info/data/locations/admin/mli/MLI> . 
[%ttlPopCount%]
[%ttlMethod%]
[%ttlSource%]";

/*
 * CURL
 */
$curlDropOneContainer = "curl --user [%userPass%] --data-urlencode \"update= DROP GRAPH [%graph%]\" [%endPoint%]\n";
$curlDropOneEmergency = "curl --user [%userPass%] --data-urlencode \"update= DELETE
 { GRAPH ?a 
  { 
    ?a <http://hxl.humanitarianresponse.info/ns/#aboutEmergency> <http://hxl.humanitarianresponse.info/data/emergencies/demo1>
  } 
}\" [%endPoint%]";
 
?>