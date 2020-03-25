# Henry Class

Command transmission class for henry turnstiles.


## Important note

This class was based on the class [henry-php]( https://github.com/juliolvfilho/henry-php) and received an important contribution from the __Henry Integration Team__.

## Examples (Text in Brazilian Portuguese)
##### Base64 biometrics is used, remember that it is necessary to configure this encapsulation in the equipment
* example_01.php: "Low level" example of sending a biometric in base64 to the equipment. Uses the __writeSocket()__ method for sending and the __listen()__ method for returning.
* example_02.php: Same functionality as the previous one, but using the __sendBiometricBase64()__ method. 
* example_03.php: Example showing how to get a base64 biometry from a device using the __getBiometricByIdBase64()__ method.
* example_04.php: Example of how to delete a device's biometrics using the deleteBiometric method.


## Contributing

Please read [CONTRIBUTING.md](https://github.com/albandes/helpdezk/blob/master/CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/your/project/tags).

## Authors

* **Rogério Albandes** - *Initial work* - [albandes](https://github.com/albandes)

See also the list of [contributors](https://github.com/albandes/helpdezk/contributors) who participated in this project.

## License

This project is licensed under the GNU General Public License v3.0 - see the [licence.txt](licence.txt) file for details


