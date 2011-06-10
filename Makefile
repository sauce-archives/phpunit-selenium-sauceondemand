all:
	@echo Please run "make release" to confirm and make a release

RELEASE = $(shell python get_release.py)

pre-release:
	test -d ../pear || echo "\n\nyou need to checkout a copy of Sauce's pear channel: https://github.com/saucelabs/pear in the parent directory"
	test -d ../pear
	which pirum || echo "\n\nyou need pirum installed: http://www.pirum-project.org/"
	which pirum

autoupdate:
	python autoupdate_package.py

release: pre-release autoupdate
	git add package.xml
	git commit -m "automatic $(RELEASE) release"
	pear package-validate
	pear package
	cd .. ;pirum add pear phpunit-selenium-sauceondemand/PHPUnit_Selenium_SauceOnDemand-$(RELEASE).tgz
	cd ../pear ;git add -A ;git commit -m "automatic $(RELEASE) release"
	echo "\n\nAll set, please cd into ../pear and run \"git push origin gh-pages\" for pushing the release"


