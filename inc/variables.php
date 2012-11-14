<?php

/*
 * Log files
 */
$logETL = '../logs/unhcr2hxl_etl.log';
$logCreator = '../logs/unhcr2hxl_creators.log';
$logDelete = '../logs/unhcr2hxl_delete.log';
$scriptCurlDrop = '../logs/unhcr2hxl_curlDropScript.txt';

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
$defaultPopulationType = 'RefugeesAsylumSeekers';

/*
 * MySQL
 */
$mysqlQueryCountRows = "SELECT COUNT(*) FROM unhcr2hxl_datarefpop ";

$mysqlQuerySources = "SELECT * FROM unhcr2hxl_sourcetranslation";
/*
$mysqlQueryAllPopulation = "SELECT DISTINCT unhcr2hxl_datarefpop.ReportDate, unhcr2hxl_settlementpcode.pcode AS settlementPcode, unhcr2hxl_countrypcode.pcode AS currentCountryPcode, 
unhcr2hxl_datarefpop.origin, unhcr2hxl_datarefpop.TotalRefPop_HH, unhcr2hxl_datarefpop.TotalRefPop_I, 
unhcr2hxl_datarefpop.DEM_04_M, unhcr2hxl_datarefpop.DEM_04_F, unhcr2hxl_datarefpop.DEM_511_M, unhcr2hxl_datarefpop.DEM_511_F, unhcr2hxl_datarefpop.DEM_1217_M, 
unhcr2hxl_datarefpop.DEM_1217_F, unhcr2hxl_datarefpop.DEM_1859_M, unhcr2hxl_datarefpop.DEM_1859_F, unhcr2hxl_datarefpop.DEM_60_M, unhcr2hxl_datarefpop.DEM_60_F 
FROM unhcr2hxl_datarefpop 
LEFT JOIN unhcr2hxl_settlement ON unhcr2hxl_datarefpop.Settlement = unhcr2hxl_settlement.Id 
LEFT JOIN unhcr2hxl_settlementpcode ON unhcr2hxl_settlement.Id = unhcr2hxl_settlementpcode.Id 
LEFT JOIN unhcr2hxl_countrypcode ON unhcr2hxl_settlement.Country = unhcr2hxl_countrypcode.Id 
WHERE (unhcr2hxl_settlement.SettlementName IS NOT NULL) 
ORDER BY ReportDate";
//echo $mysqlQueryAllPopulation;
*/
$mysqlQueryManyRows = "SELECT DISTINCT unhcr2hxl_datarefpop.ReportDate,
unhcr2hxl_datarefpop.UpdatedDate, unhcr2hxl_datarefpop.DataSource AS source, 
unhcr2hxl_settlementpcode.pcode AS settlementPcode, unhcr2hxl_countrypcode.pcode AS currentCountryPcode,
unhcr2hxl_datarefpop.origin, unhcr2hxl_datarefpop.TotalRefPop_HH, unhcr2hxl_datarefpop.TotalRefPop_I, 
unhcr2hxl_datarefpop.DEM_04_M, unhcr2hxl_datarefpop.DEM_04_F, unhcr2hxl_datarefpop.DEM_511_M, 
unhcr2hxl_datarefpop.DEM_511_F, unhcr2hxl_datarefpop.DEM_1217_M, unhcr2hxl_datarefpop.DEM_1217_F, 
unhcr2hxl_datarefpop.DEM_1859_M, unhcr2hxl_datarefpop.DEM_1859_F, unhcr2hxl_datarefpop.DEM_60_M, 
unhcr2hxl_datarefpop.DEM_60_F 
FROM unhcr2hxl_datarefpop 
LEFT JOIN unhcr2hxl_settlement ON unhcr2hxl_datarefpop.Settlement = unhcr2hxl_settlement.Id 
LEFT JOIN unhcr2hxl_settlementpcode ON unhcr2hxl_settlement.Id = unhcr2hxl_settlementpcode.Id 
LEFT JOIN unhcr2hxl_countrypcode ON unhcr2hxl_settlement.Country = unhcr2hxl_countrypcode.Id 
WHERE (unhcr2hxl_settlement.SettlementName IS NOT NULL) 
ORDER BY ReportDate DESC 
LIMIT 200";// Set this value for your tests

$mysqlQueryAllRows = "SELECT DISTINCT unhcr2hxl_datarefpop.ReportDate, unhcr2hxl_datarefpop.UpdatedDate, 
unhcr2hxl_datarefpop.DataSource AS source, unhcr2hxl_settlementpcode.pcode AS settlementPcode,
unhcr2hxl_countrypcode.pcode AS currentCountryPcode, unhcr2hxl_datarefpop.origin, 
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

$mysqlQueryPcodes = "SELECT DISTINCT unhcr2hxl_datarefpop.Settlement, 
unhcr2hxl_settlement.SettlementName AS settlementName, 
unhcr2hxl_settlementpcode.pcode AS settlementPcode, 
unhcr2hxl_countrypcode.pcode AS countryPcode
FROM unhcr2hxl_datarefpop
LEFT JOIN unhcr2hxl_settlementpcode ON unhcr2hxl_datarefpop.Settlement = unhcr2hxl_settlementpcode.Id
LEFT JOIN unhcr2hxl_settlement ON unhcr2hxl_settlement.Id = unhcr2hxl_datarefpop.Settlement
LEFT JOIN unhcr2hxl_countrypcode ON unhcr2hxl_datarefpop.Country = unhcr2hxl_countrypcode.Id
WHERE unhcr2hxl_datarefpop.Settlement !=0";

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
<[%containerUri%]> hxl:reportedBy <http://hxl.humanitarianresponse.info/data/persons/[%reporter%]> . ";
//echo $ttlContainerHeader;

$ttlContainerHeaderLocations = "<[%containerUri%]> a hxl:DataContainer . 
<[%containerUri%]> dc:date \"[%reportDate%]\"^^xsd:date . ";

$ttlSubject = "<http://hxl.humanitarianresponse.info/data/[%populationType%]/[%countryPCode%]/[%campPCode%]/[%originPCode%]/[%sex%]/[%age%]>";
$ttlSex = "[%subject%] hxl:SexCategory <http://hxl.humanitarianresponse.info/data/sexcategories/[%sex%]> . ";
$ttlAge = "[%subject%] hxl:AgeGroup <http://hxl.humanitarianresponse.info/data/agegroups/unhcr/[%age%]> . ";
$ttlPersonCount = "[%subject%] hxl:personCount \"[%personCount%]\"^^xsd:integer . ";
$ttlHouseholdCount = "[%subject%] hxl:householdCount \"[%householdCount%]\"^^xsd:integer . ";
$ttlMethod = "[%subject%] hxl:method \"[%method%]\" . ";
$ttlSource = "[%subject%] hxl:source <http://hxl.humanitarianresponse.info/data/organisations/[%source%]> . ";

$ttlPopDescription = "[%subject%] hxl:atLocation <http://hxl.humanitarianresponse.info/data/locations/apl/[%countryPCode%]/[%campPCode%]> . 
[%subject%] rdf:type hxl:RefugeesAsylumSeekers .
[%ttlSex%]
[%ttlAge%]
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