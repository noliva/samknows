**Test for SamKnows**

I have created a command using Symfony console component.

Can be executed like:

`php app app:import http://tech-test.sandbox.samknows.com/php-2.0/testdata.json --host=127.0.0.1 --dbname=samknows --username=root --password=root`

I have decided to use a temporary mysql table and do all the heavy operations from there to avoid memory issues with the script.

Also for time, I haven't been able to calculate the median.

_**Things I would improve from my test.**_

* If I had a bit more time, I would have created a functional Test for the service.
* Also I would have refactored the Fetcher to be constructed with a type (Json, XML, etc..) and a locator (http, file, etc ..)
* I would have extracted the queries to not know which db implementation is being used, and passed it to the service too.

_**Others**_

It would have been nice to get a acceptance criteria on the task, so I could create a test to make sure the acceptance criteria is met.