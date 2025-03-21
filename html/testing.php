<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <link rel="icon" href="/t.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <title>Twokie - New Submission</title>
    <link rel="stylesheet" href="main.css">
    <!-- <link rel="stylesheet" href="submission.css"> -->
    <script type="module">
        (async ({chrome, netscape}) => {

            // add Safari polyfill if needed
            if (!chrome && !netscape)
                await import('https://unpkg.com/@ungap/custom-elements');

            const {default: HighlightedCode} =
                await import('https://unpkg.com/highlighted-code');

            // bootstrap a theme through one of these names
            // https://github.com/highlightjs/highlight.js/tree/main/src/styles
            HighlightedCode.useTheme('github-dark');
        })(self);
    </script>
</head>

<form action="submission.php" method="POST">
                <label for="name">Enter your code!:</label>
                <textarea id="name" name="code" required value="<?= $txt ?>" is="highlighted-code" placeholder="return True


# The input field is in Python, which cares about indentation. Here, just use 4 spaces as a substitute for <TAB>" onkeydown="return stopTab(event);"></textarea>
                <input type="hidden" name="game_id" value="1">
                <button type="submit" name="submitCode" value="submit">Submit</button>
            </form>