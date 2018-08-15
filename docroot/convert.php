<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="container flex-column">

    <div class="row">
        <div class="col col-md-6">
            <h1>HTML table converter</h1>
            <p>This little app converts specific types of HTML tables into a specially formatted JSON structure. It then sends that JSON to a Drupal REST endpoint to programmatically create the Drupal node. Its only use right now is for converting standard event attribute definition tables into attribute definition nodes.</p>
             <button class="btn btn-outline-info" type="button" data-toggle="collapse" data-target="#usage-notes" aria-expanded="false" aria-controls="usage-notes">Usage notes</button>
             <div id="usage-notes" class="collapse mt-3">
                <div class="card card-body border-info text-secondary">
                    <ul>
                        <li>Only one event term ID per table (we can’t really distinguish what attributes belong to what events by just the table HTML. that will have to be a human process).</li>
                        <li>Table must only have two columns that contain the <b>Attribute Name</b> and <b>Description</b>.</li>
                        <li>App converts any elements in either <code>&lt;td&gt;</code> to just plain text and removes any spacing characters (spaces, tabs, newline).</li>
                        <li>In the Name column, which should be the attribute name, app explodes the text and takes the first string. This is done because many tables have the data type in the column as well.</li>
                        <li>The Description column is always entered as the Short Description since that’s essentially what those are and it’s a required field (unlike the Long Description / Body field).</li>
                        <li>App does not support detecting hyperlinks in Description text. For now, that will have to be a human process.</li>
                        <li>App accepts tables with or without <code>&lt;tbody&gt;</code> and ignores <code>&lt;th&gt;</code> elements.</li>
                    </ul>
                </div>
             </div>
        </div> <!-- .col -->
    </div> <!-- .row -->

    <div class="row mt-5">

        <div class="col col-md-6">

            <form id="table-convert-form" action="convert-results.php" method="post">
                <div class="form-group">
                    <label for="table-html">Table HTML</label><br/>
                    <textarea id="table-html" name="table-html" class="form-control" rows="10" required></textarea>
                </div>

                <div class="form-group">
                    <label for="term_tid">Event term ID</label><br/>
                    <input id="term_tid" name="term_tid" type="text" class="form-control" required></textarea>
                </div>

                <fieldset class="form-group">
                    <label>Endpoint to insert nodes</label><br/>
                    <div class="form-check">
                        <input id="endpoint-local" name="endpoint" class="form-check-input" type="radio" checked="checked" value="http://newrelic.dev.dd:8083/api/node"></input>
                        <label for="endpoint-local" class="form-check-label">http://newrelic.dev.dd:8083/api/node</label>
                    </div>

                    <div class="form-check">
                        <input disabled id="endpoint-dev" name="endpoint" class="form-check-input" type="radio" value="https://docs-dev.newrelic.com/api/node"></input>
                        <label for="endpoint-dev" class="form-check-label">https://docs-dev.newrelic.com/api/node</label>
                    </div>
                    <div class="form-check">
                        <input disabled id="endpoint-prod" name="endpoint" class="form-check-input" type="radio" value="https://docs.newrelic.com/api/node"></input>
                        <label for="endpoint-prod" class="form-check-label">https://docs.newrelic.com/api/node</label>
                    </div>
                </fieldset>

                <input type="submit" id="submit" class="btn btn-primary"></input>

            </form>

        </div> <!-- .col -->

    </div> <!-- .row -->

</div> <!-- .container -->

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>

</html>
