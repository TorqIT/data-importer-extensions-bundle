---
title: Data Importer
---
 
# Torq IT Data Importer Extensions

This extension adds a number of additional features to the [Pimcore Data Importer](https://github.com/pimcore/data-importer) extension.

## Path Syntax

A number of our extensions make use of the **Path** syntax that allows for Paths to be created based on values in the import. 

Take for example an Excel File:
| Year | Make | Model | Color |
| ---  | ---  | ---   | ---   |
| 2015 | GMC  | Sierra| White |
| 2001 | Chevrolet | Silverado | Blue |

To build the Path `/Products/Cars/GMC/Sierra/2015` using Path Syntax would be `/Products/Cars/$[1]/$[2]/$[0]`. The numerical values correspond to the indexes of the values in the Excel file (starting at 0).

For an XML file:

```
<Cars>
    <Car>
        <Make>GMC</Make>
        <Model>Sierra</Model>
        <Year>2015</Year>
        <Color>White</Color>
    </Car>
    <Car>
        <Make>Chevrolet</Make>
        <Model>Silverado</Model>
        <Year>2001</Year>
        <Color>Blue</Color>
    </Car>
</Cars>
```
the **Path Syntax** would use the Attribute names instead `/Products/Cars/$[Make]/$[Model]/$[Year]`



## Data Interpreters

Data Interpreters are the supported "File Formats" that the Data Importer bundle can use. We've added a few of our own.

### Advanced XLSX Interpreter

The Advanced XLSX interpreter makes a few improvements over the default XLSX interpreter.

This interpreter uses `box/spout` as the Excel parser. This will soon be changing to `openspout/openspout` as Box is now archived. Box/Open Spout XLSX parsing uses **much** less memory than the default XLSX parses which makes use of `PHPOffice`. We've seen files that required >4GB RAM on PHPOffice use less than 50MB with Box. We've also detected a memory leak in some cases with the PHPOffice implementation where RAM gets allocated on the server and never released.


| Configuration Option   | Description                                    | 
| ---------------------- | ---------------------------------------------- |
| Unique Column Indexes  | Accepts a comma separated list of column indexes to treat as unique values. Used to filter the rows in an excel file. For example an excel file with the headers `Brand,Model,SubModel` and you want to import a unique `Brand` object for each new `Brand` you encounter. In this case, use value `0` to only take unique values from the first column in the Excel file. If you want to create a data object for each `Brand` and `Model` use `0,1` as the value.                                           |
| Row Filter             | This accepts a [Symfony Expression](https://symfony.com/doc/current/reference/formats/expression_language.html) to be applied to the rows of the Excel file. Each row in the Excel file get's handed to the expression evaluator as a variable named `row`. The expression `row[0] == 'Apple'` would only process rows where the value of the first column is Apple.


### Bulk XLSX Interpreter

This Bulk XLSX Interpreter has the same options as the Advanced XLSX Interpreter. The main difference is that the Excel file gets converted to a CSV and loaded into the Data Importer queue using `LOAD LOCAL INFILE`. This **VERY DRASTICALLY** improves the performance of loading the queue table. We've seen 200K rows loaded in <5s. Our experience with a 16GB RAM server shows that Excel files over 30K rows often are not imported successfully by the default XLSX Interpreter.

**This Feature Requires the Database Server to be configured to permit local infile / infile permissions!**

See [MySQL Documentation](https://dev.mysql.com/doc/refman/8.0/en/load-data-local-security.html#load-data-local-configuration) regarding `LOCAL INFILE`.

## Data Loaders

### SQL Data Loader

The SQL Data Loader uses [DBAL](https://www.doctrine-project.org/projects/dbal.html) to allow data to be loaded from a SQL source. Connections to any database supported by DBAL will work provided they are configured correctly inside of `database.yaml`.

To set up a SQL source

1. Create a new connection in `database.yaml` or if using the Pimcore database skip this step. ![SQL Loader Configuration](docs/img/sql_loader_config.png)
2. Select the correct connection from the **Connection Name** dropdown
3. Paste a valid SQL `SELECT` query into the **SQL** text area.
4. Ensure to select **JSON** under File Format! This loader produces a JSON file as part of loading the SQL.


## Data Targets

Data Targets control where data flows as its being mapped to Data Objects.

### Advanced Classification Store

This is the same as the [Classification Store](https://pimcore.com/docs/platform/Data_Importer/Configuration/Mapping_Configuration/Data_Target/#classification-store) Data Target except it adds the `Overwrite` options as seen on the `Direct` Data Target. 

### Image Gallery Appender

This can be used to add an image into an Image Gallery field.

### Property

This is used to set a property on a Data Object.


## Operators

### Constants

This operator simply returns a constant string. Useful if wanting to control `OBJECT_TYPE` object or variant.

### SafeKey

This ensure that a value is cleaned to be a valid Key value.

### Import Asset Advanced

This allows two additional pieces of functionality when importing an asset:

**Path** Uses the **Path Syntax** described above to store the asset in a specified folder.

**URL Property** Specifies the name of the property on the asset to store the source URL the asset was captured from.

## Element Loading

### Advanced Path Strategy

This allows loading objects using the **Path** syntax described earlier in this ReadMe.

Using the example Excel file in the **Path** section you could load the Data Object at `/Products/Cars/GMC/Sierra/2015` using Path Syntax `/Products/Cars/$[1]/$[2]/$[0]`. 

### Property

This allows a data object to be loaded based on the value of a property stored on it.

**This assumes that the property value is unique**. If a non-unique value exists, it'll be a random object returned that matches the criteria.


## Element Creation

### Advanced Parent Strategy

This allows locating objects using the **Path** syntax described earlier in this ReadMe.

Using the example Excel file in the **Path** section you could create a Data Object with parent `/Products/Cars/GMC/Sierra/2015` using Path Syntax `/Products/Cars/$[1]/$[2]/$[0]`. 
