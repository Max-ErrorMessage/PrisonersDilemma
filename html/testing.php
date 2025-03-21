<!DOCTYPE html>
<html lang="en">
<head>
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