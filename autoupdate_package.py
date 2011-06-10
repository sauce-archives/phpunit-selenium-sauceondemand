import datetime
from xml.dom import minidom

package = minidom.parse("package.xml")
current_date = package.getElementsByTagName("date")[0].childNodes[0].data
current_release = package.getElementsByTagName("version")[0].getElementsByTagName("release")[0].childNodes[0].data
current_api = package.getElementsByTagName("version")[0].getElementsByTagName("api")[0].childNodes[0].data


print "Current date: %s" % current_date
date = datetime.datetime.today().strftime("%Y-%m-%d")
print "Updating Date to %s" % date

print "Current release: %s" % current_release
release = raw_input("Please provide the next release: ")
print "Updating release to %s" % release

print "Current API version: %s" % current_api
confirm = raw_input("Change API version? [y/N]: ")
if confirm in ["y", "Y"]:
    api = raw_input("Please provide the next version: ")
    print "Updating API version to %s" % api
else:
    api = ""

confirm = raw_input("Confirm updates? [Y/n]: ")
if confirm in ["y", "Y", ""]:
    package.getElementsByTagName("date")[0].childNodes[0].data = date
    package.getElementsByTagName("version")[0].getElementsByTagName("release")[0].childNodes[0].data = release
    if api:
        package.getElementsByTagName("version")[0].getElementsByTagName("api")[0].childNodes[0].data = api
    with open("package.xml", "w") as new:
        new.write(package.toxml())

print release
