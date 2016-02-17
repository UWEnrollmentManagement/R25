[![Build Status](https://travis-ci.org/UWEnrollmentManagement/R25.svg?branch=master)](https://travis-ci.org/UWEnrollmentManagement/R25)
[![Code Climate](https://codeclimate.com/github/UWEnrollmentManagement/R25/badges/gpa.svg)](https://codeclimate.com/github/UWEnrollmentManagement/R25)
[![Test Coverage](https://codeclimate.com/github/UWEnrollmentManagement/R25/badges/coverage.svg)](https://codeclimate.com/github/UWEnrollmentManagement/R25/coverage)
[![Latest Stable Version](https://poser.pugx.org/uwdoem/r25/v/stable)](https://packagist.org/packages/uwdoem/r25)

UWDOEM/R25
=============

Smoothly poll the University of Washington's [R25 Classroom Scheduling Service](https://wiki.cac.washington.edu/display/r25ws/R25+Web+Service+Client+Home+Page).

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

Installation
------------

This library is published on packagist. To install using Composer, add the `"uwdoem/r25": "1.*"` line to your "require" dependencies:

```
{
    "require": {
        "uwdoem/r25": "0.*"
    }
}
```

Of course it is possible to use *R25* without Composer by downloading it directly, but use of Composer to manage packages is highly recommended. See [Composer](https://getcomposer.org/) for more information.

Troubleshooting
---------------

This library *will* throw warnings and exceptions when it recognizes an error. Turn on error reporting to see these. For errors involving *cURL*, *SSL*, and or script execution halts/no output, see [UWEnrollmentManagement/Connection](https://github.com/UWEnrollmentManagement/Connection) troubleshooting.

Compatibility
-------------

* R25 Classroom Scheduling Web Service v2


Requirements
------------

* PHP 5.5, 5.6, 7.0
* uwdoem/connection 2.*


Todo
----

See GitHub [issue tracker](https://github.com/UWEnrollmentManagement/R25/issues/).


Getting Involved
----------------

Feel free to open pull requests or issues. [GitHub](https://github.com/UWEnrollmentManagement/R25) is the canonical location of this project.

Here's the general sequence of events for code contribution:

1. Open an issue in the [issue tracker](https://github.com/UWEnrollmentManagement/R25/issues/).
2. In any order:
  * Submit a pull request with a **failing** test that demonstrates the issue/feature.
  * Get acknowledgement/concurrence.
3. Revise your pull request to pass the test in (2). Include documentation, if appropriate.

PSR-2 compliance is enforced by [CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) in Travis.
