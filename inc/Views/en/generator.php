<section class="box section section--generator">
    <form action="" method="POST">
        <p>Paste a password or another senstive information and we will generate a onetime link for you to share securely. Encryption and decryption happens in the browser, we never store plain-text information. The information will be stored for up to 7 days.</p>

        <textarea placeholder="Sensitive information..."></textarea>

        <p>Leaving the secret phrase field empty will cause a random secret phrase generation which will be included in the generated link.
        </p>

        <input type="text" placeholder="Secret phrase">

        <button class="full">🔗 Generate</button>
    </form>
</section>

<section class="box section section--result" style="display:none">
    <p>Your sensitive information has been saved. You can share the following link and once it is opened it will be deleted.</p>

    <input type="text" placeholder="https://....">

    <div class="side-right">
        <button>📋 Copy</button>
        <button>🗑️ Delete</button>
    </div>

    <p>
        <h2>F.A.Q.</h2>
        <ul>
            <li>
                <strong>Will the information be deleted by just visiting the link?</strong>
                <p>No. The user needs to confirm that he wants to view the senstive information before it being retrieved and deleted from the server.</p>
            </li>
            <li>
                <strong>For how long will this link be available?</strong>
                <p>The link will be available for 7 days if unopened. Once the sensitive message has been opened, it will be instantly deleted.</p>
            </li>
            <li>
                <strong>Can I delete it before the time limit?</strong>
                <p>If you click the delete button now, it will be deleted instantly. If you leave this page, the only option to delete it will be to open the sensitive message by visiting the link.</p>
            </li>
            <li>
                <strong>How is the information stored?</strong>
                <p>The sensitive information is encrypted in your browser and stored as ilegible text in our server. In order to decypher it, the user must input the secret phrase. The decryption process is also done in the browser. We do not store the secret phrase.</p>
            </li>
        </ul>
    </p>
</section>
