# ETL of the UNHCR population table to HXL rdf.

A set of PHP scripts for translating the UNHCR population database in the **Humanitarian eXchange Language (HXL)** (see [project website](https://sites.google.com/site/hxlproject/) and [hxl.humanitarianresponse.info](http://hxl.humanitarianresponse.info) ).

This current version aims at providing real data for testing other HXL tools. The script takes benefit of a real case but the provided data is not formatted according the need of the HXL standard. So the data must be processed and translated to allow the recognition of locations, persons and organisations.
That's why this project includes a temporary solution that checks values against the triple store and translates values thanks to correspondence tables. The aim is to build correct URIs or at least searchable URIs so that other applications such as the HXL Dashboard can work.
Some logs collect errors and failures to recognize values. It is intended to help to correct or to complete the correspondence tables or even to give an overview of the gap between real data and the HXL standard requirement to allow a live ETL.

Requires:
- PHP 5.2.9 minimum.
- The database dump from UNHCR and three temporary tables for values translations (undisclosed).
- Parameters to access the database and the triple store (undisclosed).
