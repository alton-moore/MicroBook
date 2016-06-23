<?PHP


# The program constants will be here instead of in a config file.  Remember to delete the microbook.php~ file
# after you edit this program.
#
# Basically the whole thing is here, except for the data file, which should be hidden in a directory and named
# something like microbook_247t27/microbook.dat  The first line of the data file contains the last entry number
# assigned, and this program merely reads from that number back until the maximum number of days or entries is
# reached.  Right now there are no more lines in the data file.  Remember to set the owner of the microbook.dat
# directory and file to the user that runs the apache process, so that it can update the file when necessary
# and write new files into the directory.


$DAYS_TO_SHOW            = 21;  # How many days of entries to show.
$MAXIMUM_ENTIRES_TO_SHOW = 30;  # Maximum number of entries applied in addition to maximum number of days.
$SUBDIRECTORY_NAME       = "microbook_5848h2f51s/";
$DATA_FILE_NAME          = $SUBDIRECTORY_NAME . "microbook.dat";

$last_entry_number = 0;
if (file_exists($DATA_FILE_NAME))
  $last_entry_number = trim(file_get_contents($DATA_FILE_NAME));

# Did the user just submit a new entry?  If so, upload the file and update everything appropriately.
# Make them click to return to the program so that the auto-refreshing doesn't try to submit their post again.
$text_area = $_POST["text_area"];
if (strlen($text_area) > 0)  # Did they enter text into the text box?
  {
  $last_entry_number++;  # Update the last entry number on file first.
  file_put_contents($DATA_FILE_NAME,"$last_entry_number\n");
  #
  # The file is uploaded to /tmp, so move it to where we want it now.
  $uploaded_file_name = "";
  $extension = substr(basename($_FILES["uploaded"]["name"]),-3);
  $target = "${SUBDIRECTORY_NAME}$last_entry_number." . $extension;
  if (move_uploaded_file($_FILES["uploaded"]["tmp_name"],$target))  # Ignore any errors while uploading files, since here they are usually because no file was uploaded.
    $uploaded_file_name = "$last_entry_number." . $extension;
  #
  $signature = $_POST["signature"];
  $output_file_name = "${SUBDIRECTORY_NAME}$last_entry_number.dat";  # Now write the file with the image file name (if any) and text area in it.
  file_put_contents($output_file_name,"$uploaded_file_name\n" . substr(date("r"),0,25) . "  ($signature)\n$text_area\n");
  echo "Thanks for posting!  Click <A HREF=\"microbook.php\">here</A> to return to the page.\n";
  exit;
  }

# Write out the page header, artwork/title, whatever.
echo "<HTML>\n";
echo "<HEAD>\n";
echo "<TITLE>Sample Microbook Page</TITLE>\n";
?>
<script type="text/javascript">
var timeout = setTimeout("location.reload(true);",1000000);
function resetTimeout() {
clearTimeout(timeout);
timeout = setTimeout("location.reload(true);",1000000); }
</script>
<?PHP
echo "</HEAD>\n";
echo "<BODY>\n";
echo "<STYLE TYPE=\"text/css\">img { width: 480px; }</STYLE>\n";
echo "<CENTER><H2>Sample Microbook Page</H2></CENTER>\n";
echo "<P>\n";
echo "<HR>\n";
echo "<P>\n";

# First present the form so the user can add a new entry if they wish.
echo "<FORM ENCTYPE=\"multipart/form-data\" ACTION=\"microbook.php\" METHOD=\"POST\">\n";
echo "<TABLE ROWS=\"1\" COLUMNS=\"2\">\n";
echo "<TR>\n";
$prompt_string = "Please enter your post here, and remember to sign your name at the end!  This page refreshes itself every 10 minutes.";
echo "<TD><TEXTAREA ROWS=\"5\" COLS=\"60\" NAME=\"text_area\" onfocus=\"if (this.value == '$prompt_string') this.value = '';\" onblur=\"if (this.value == '') this.value = '$prompt_string';\">$prompt_string</TEXTAREA></TD>\n";

echo "<TD>Picture file to upload (optional): <INPUT NAME=\"uploaded\" TYPE=\"file\"></TD>\n";
echo "</TR>\n";
echo "</TABLE>\n";
echo "Signature (optional but nice): <INPUT TYPE=\"text\" SIZE=\"24\" NAME=\"signature\"><BR>\n";
echo "<INPUT TYPE=\"submit\" VALUE=\"Post entry\">\n";
echo "</P>\n";
echo "</FORM>\n";
echo "<HR SIZE=5>\n";
        
# Now read through all entries on file and display on the screen.
$current_entry = $last_entry_number;
while (($current_entry > ($last_entry_number = $MAXIMUM_ENTRIES_TO_SHOW)) && ($current_entry > 0))
  {
  $filename = "${SUBDIRECTORY_NAME}$current_entry.dat";
  if (!file_exists($filename))
    break;
  $input_file = fopen($filename,"r");
  $image_file_name = trim(fgets($input_file));  # The first line is the name of the saved image file, if any.
  #echo "<PRE>\n";
  $first_line = rtrim(fgets($input_file));
  echo "$first_line<BR>\n";
  while (!feof($input_file))  # Now read the lines of narrative that the user typed, if any.
    {
    $text_area = rtrim(fgets($input_file));
    if (strlen($text_area) < 5)
      continue;
    echo "$text_area\n";
    }
  #echo "</PRE>\n";
  fclose($input_file);
  #
  if (strlen($image_file_name) > 0)
    echo "<BR><IMG SRC=\"${SUBDIRECTORY_NAME}$image_file_name\">\n";
  #
  $current_entry--;
  echo "<HR>\n";
  }

echo "Microbook written by Alton Moore (and available from sourceforge.net and GitHub)<BR>\n";
echo "</BODY>\n";
echo "</HTML>\n";
?>
