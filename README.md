[![Build Status](https://travis-ci.org/UWEnrollmentManagement/Person.svg?branch=master)](https://travis-ci.org/UWEnrollmentManagement/Person)
[![Code Climate](https://codeclimate.com/github/UWEnrollmentManagement/Person/badges/gpa.svg)](https://codeclimate.com/github/UWEnrollmentManagement/Person)
[![Test Coverage](https://codeclimate.com/github/UWEnrollmentManagement/Person/badges/coverage.svg)](https://codeclimate.com/github/UWEnrollmentManagement/Person/coverage)
[![Latest Stable Version](https://poser.pugx.org/uwdoem/person/v/stable)](https://packagist.org/packages/uwdoem/person)

UWDOEM/Person
=============

Smoothly poll the University of Washington's [Person Web Service](https://wiki.cac.washington.edu/display/pws/Person+Web+Service) (PWS) and [Student Web Service](https://wiki.cac.washington.edu/display/SWS/Student+Web+Service) (SWS) to aggregate data on a given affiliate, using X.509 certificate authentication.

For example:

``` 
    // Intialize the required settings
    define('UW_WS_BASE_PATH', '/path/to/my/private.key');
    define('UW_WS_SSL_KEY_PATH', '/path/to/my/private.key');
    define('UW_WS_SSL_CERT_PATH', '/path/to/my/public_cert.pem');
    define('UW_WS_SSL_KEY_PASSWD', 'myprivatekeypassword');  // Can be blank for no password: ''
    
    /* Query the web services */
    $student = Student::fromStudentNumber("1033334");
    
    echo $student->getAttr("RegisteredFirstMiddleName");
    // "JAMES AVERAGE"
    
    echo $student->getAttr("UWNetID");
    // "javerage"
    
    /* Retrieve registration for James Average*/
    $registrations = $student->registrationSearch("2009", "summer");
    echo $registrations[0]["CurriculumAbbreviation"];  // "TRAIN"
    echo $registrations[0]["CourseNumber"];  // "100"
    
    /* Retrieve employee information from the web services */
    $employee = Employee::fromUWNetID("jschilz");
    
    echo $employee->getAttr("Department1");
    // "Student Financial Aid Office"
    
    echo $employee->getAttr("Title1");
    // "Web Developer"

```

Notice
------

This is *not* an official library, endorsed or supported by any party who manages or owns information accessed via PWS or SWS. This library is *not* endorsed or supported by the University of Washington Department of Enrollment Management.

Installation
------------

This library is published on packagist. To install using Composer, add the `"uwdoem/person": "1.*"` line to your "require" dependencies:

```
{
    "require": {
        "uwdoem/person": "1.*"
    }
}
```

Of course it is possible to use *Person* without Composer by downloading it directly, but use of Composer to manage packages is highly recommended. See [Composer](https://getcomposer.org/) for more information.

Use
---

This client library provides four data-container classes: `Person`, `Student`, `Employee`, and `Alumni`.

If you have not already done so, follow PWS's instructions on [getting access to PWS](https://wiki.cac.washington.edu/display/pws/Getting+Access+to+PWS). A similar set of steps will allow you to [gain access to SWS](https://wiki.cac.washington.edu/display/SWS/Getting+Access+to+SWS). You'll need to place both the private private key you created and also the university-signed certificate on your web server, with read-accessibility for your web-server process.

Before querying the web services, you must first initialize the connection by calling `::createInstance`:

```
    // Intialize the required settings
    define('UW_WS_BASE_PATH', '/path/to/my/private.key');
    define('UW_WS_SSL_KEY_PATH', '/path/to/my/private.key');
    define('UW_WS_SSL_CERT_PATH', '/path/to/my/public_cert.pem');
    define('UW_WS_SSL_KEY_PASSWD', 'myprivatekeypassword');  // Can be blank for no password: ''
```

The terms `UW_WS_SSL_KEY_PATH` and `UW_WS_SSL_CERT_PATH` correspond to the absolute locations of your private key and university-signed certificate. The `UW_WS_SSL_KEY_PASSWD` corresponds to the string which unlocks your private key; if your key does not have a password then use a blank string, eg: `''`.

The term `UW_WS_BASE_PATH` corresponds to the base URL shared by UW web services. Currently this is either `"https://ws.admin.washington.edu/"` for the production-access web services, or `"https://wseval.s.uw.edu/"` for the testing/development-access web services.

You may now issue queries against the web service:

```
    // Queries PWS/SWS for a student with StudentNumber "1033334".
    $student = Student::fromStudentNumber("1033334");
    
    // If no such student was found, then $student is null
    if ($student != null) {
        echo $student->getAttr("RegisteredFirstMiddleName");
    }
```

In the case above, there does exist a student with StudentNumber "1033334": one of the university's notional test students. So this script will output "JAMES AVERAGE".

The following methods may be used to query the database:
```
    // Available to Person, and all subclasses of Person
    $person = Person::fromUWNetID($uwnetid);
    $person = Person::fromUWRegID($uwregid);
    $person = Person::fromIdentifier("uwregid", $uwregid);
    $person = Person::fromIdentifier("uwnetid", $uwnetid);
    $person = Person::fromIdentifier("employee_id", $employeeid);
    $person = Person::fromIdentifier("student_number", $studentnumber);
    $person = Person::fromIdentifier("student_system_key", $studentsystemkey);
    $person = Person::fromIdentifier("development_id", $developmentid);
    
    // Available only to Student
    $student = Student::fromStudentNumber($studentnumber);
    $registrations = $student->registrationSearch($year, $quarter, [$extraSearchTerms]);
    
    // Available only to Employee
    $employee = Employee::fromEmployeeID($employeeid);
    
    // Available only to Alumni
    $alumni = Alumni::fromDevelopmentID($developmentid);
```

You can cast between classes each of the container classes' `::fromPerson` method:
```
    $person = Person::fromUWNetID($uwnetid);
    
    // Cast the Person object into a Student
    $person = Student::fromPerson($person);
```

The `::hasAffiliation` method can tell you whether a given person is a student, employee, and/or alumni:
```
    $person = Person::fromUWNetID($uwnetid);
    
    // The ::hasAffiliation method check is useful, but not required:
    if ($person->hasAffiliation("employee") {
        $person = Employee::fromPerson($person);
    }
```

Use `::getAttr` to retrieve an attribute from a person:
```
    $person = Person::fromUWNetID($uwnetid);
    $displayName = $person->getAttr("DisplayName");
    
    $person = Student::fromPerson($person);
    $academicDepartment = $person->getAttr("Department1");

```

The `Student::registrationSearch` method returns an array of [Registration Resources](https://wiki.cac.washington.edu/display/SWS/Registration+Resource+V5), in associative array format:
```
   $student = Student::fromStudentNumber("1033334");
   $registrations = $student->registrationSearch("2009", "summer");
   
   foreach ($registrations as $registration) {
       echo $registration["CurriculumAbbreviation"];
       echo $registration["CourseNumber"];
   }
```

You can include optional parameters in your registration search, per the [Registration Search Resource spec](https://wiki.cac.washington.edu/display/SWS/Registration+Search+Resource+v5):
```
    $student = Student::fromStudentNumber("1033334");
    $registrations = $student->registrationSearch("2009", "summer", ["is_active" => "true"]);
```

Exposed Attributes
------------------

The container classes expose the following attributes, corresponding to those described in [this PWS glossary](https://wiki.cac.washington.edu/display/pws/PWS+Attribute+Glossary):

```
    Exposed by all classes:
        "DisplayName"
        "IsTestEntity"
        "RegisteredFirstMiddleName"
        "RegisteredName"
        "RegisteredSurname"
        "UIDNumber"
        "UWNetID"
        "UWRegID"
        "WhitepagesPublish"
        
    Exposed only by Employee:
        "EmployeeID"
        "Address1"
        "Address2"
        "Department1"
        "Department2"
        "Email1"
        "Email2"
        "Fax"
        "Name"
        "Phone1"
        "Phone2"
        "PublishInDirectory"
        "Title1"
        "Title2"
        "TouchDial"
        "VoiceMail"
    
    Exposed only by Student:
        "StudentNumber"
        "StudentSystemKey"
        "Class"
        "Department1"
        "Department2"
        "Department3"
        "Email"
        "Name"
        "Phone"
        "PublishInDirectory"

    Exposed by Student, when SWS access is enabled:
        "RegID"
        "FirstName"
        "LastName"
        "StudentName"
        "EmployeeID"
        "BirthDate"
        "Gender"
        "DirectoryRelease"
        "LocalAddress"
            "Line1"
            "Line2"
            "City"
            "State"
            "Zip"
            "Country"
            "PostalCode"
        "PermanentAddress"
            "Line1"
            "Line2"
            "City"
            "State"
            "Zip"
            "Country"
            "PostalCode"
        "LocalPhone"
        "PermanentPhone"
        "Veteran"
            "Code"
            "Description"
        "LastEnrolled"
            "Href"
            "Year"
            "Quarter"
        "Notices"[]
            "RegID"
            "Href"
        "PersonFinancial"[]
            "RegID"
            "Href"
        "Resident"
        "VisaType"
        "TestScore"[]
            "RegID"
            "Href"

    Exposed only by Alumni:
        "DevelopmentID"

```
Troubleshooting
---------------

This library *will* throw warnings and exceptions when it recognizes an error. Turn on error reporting to see these. For errors involving *cURL*, *SSL*, and or script execution halts/no output, see [UWEnrollmentManagement/Connection](https://github.com/UWEnrollmentManagement/Connection) troubleshooting.

Compatibility
-------------

* Person Web Service v1
* Student Web Service v5


Requirements
------------

* PHP 5.5, 5.6, 7.0
* uwdoem/connection 2.*


Todo
----

See GitHub [issue tracker](https://github.com/UWEnrollmentManagement/Person/issues/).


Getting Involved
----------------

Feel free to open pull requests or issues. [GitHub](https://github.com/UWEnrollmentManagement/Person) is the canonical location of this project.

Here's the general sequence of events for code contribution:

1. Open an issue in the [issue tracker](https://github.com/UWEnrollmentManagement/Person/issues/).
2. In any order:
  * Submit a pull request with a **failing** test that demonstrates the issue/feature.
  * Get acknowledgement/concurrence.
3. Revise your pull request to pass the test in (2). Include documentation, if appropriate.

PSR-2 compliance is enforced by [CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) in Travis.
