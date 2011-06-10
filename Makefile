all:
	@echo Please run "make release" to confirm and make a release

RELEASE = $(shell python get_release.py)

autoupdate:
	python autoupdate_package.py

release: autoupdate
	git add package.xml
	git commit -m "automatic $(RELEASE) release"
	pear package-validate
	pear package
	cd ..
	pirum add pear phpunit-selenium-sauceondemand/PHPUnit_Selenium_SauceOnDemand-$(RELEASE).tgz
	cd pear
	git add -A
	git commit -m "automatic $(RELEASE) release"
	git push origin gh-pages


