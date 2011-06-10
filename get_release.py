from xml.dom import minidom

package = minidom.parse("package.xml")
current_release = package.getElementsByTagName("version")[0].getElementsByTagName("release")[0].childNodes[0].data

print current_release
