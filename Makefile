all:
	@echo Please run "auto release" to confirm and make a release

autoupdate:
	python autoupdate_package.py

release: autoupdate
	pear package-validate
	pear package
	pirum add pear phpunit-selenium-sauceondemand/PHPUnit_Selenium_SauceOnDemand-1.2.4.tgz
	git add package.xml
	git commit -m "automatic release"
	git push origin gh-pages)


