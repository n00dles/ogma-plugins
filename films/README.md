## Sample Film Database and Plugin

A sample database of 1000 records of movies from the MySQL sample databases for testing the system backend.

Film table can queried using normal methods to view the records.

Each record contains: 

* title - Film Title 
* description - Short description of film. 
* year - Year of release
* duration - Rental Duration
* rate - Rating
* length - Length in Minutes
* cost - Cost
* features - Additional features. 

Cached fields are: title,year, duration, rate, length, cost, features. 



### Installation

Copy the 'addins' folder to ROOT/addins  
Copy the 'data' folder to ROOT/data   

Go to Admin panel, Activate "Film" plugin

### Usage 

    // return a 25 random films
    $films = new Query('film');
    $records = $films->getCache()->randomize()->top(25); 
    foreach ($records as $record){
      echo "Title: ".$record['title]."<br/>";
      echo "Description: ".$record['description]."<br/>";
      echo "Year: ".$record['year]."<br/>";
      echo "<br/><br/>";
    }

