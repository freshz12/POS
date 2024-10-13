<style>
    body {
        margin: 0;
        overflow: hidden;
    }

    .checkin-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 60vh;
        /* background-color: #f8f9fa; */
    }

    .body-header {
        margin-bottom: 0;
        font-weight: 700;
        display: inline-block;
        margin-top: 3px;
        color: #34395e;
    }

    .body-header h1 {
        font-size: 30px;
        margin-top: 25px;
        display: flex;
        justify-content: center;
    }

    .circle-button {
        width: 300px;
        height: 300px;
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        /* Stack icon and text vertically */
        justify-content: center;
        align-items: center;
        font-size: 1.5rem;
        color: white;
        background-color: #007bff;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s;
        text-align: center;
    }

    .circle-button:hover {
        background-color: #0056b3;
    }

    .circle-button i {
        font-size: 120px;
        margin-bottom: 10px;
        display: block;
    }
</style>
