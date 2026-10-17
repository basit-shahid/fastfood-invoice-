/* Register interactions. Prices and availability are still resolved by the server at checkout. */
(() => {
    "use strict";
    const $ = (id) => document.getElementById(id);
    const config = window.counterflowRegister;
    const form = $("checkout-form");
    // Keep a native POST path available if this module fails after items have been added.
    form.noValidate = true;
    const listenMedia = (media, callback) => {
        if (typeof media.addEventListener === "function")
            media.addEventListener("change", callback);
        else if (typeof media.addListener === "function")
            media.addListener(callback);
    };
    function showFeedback(message) {
        $("checkout-feedback").textContent = message;
        $("checkout-feedback").scrollIntoView({ block: "nearest" });
    }

    const cards = [...document.querySelectorAll(".pos-item-card")];
    const cart = [];
    let category = "all";
    let editingItem = null;
    let submitting = false;
    let checkoutComplete = false;
    let checkoutUncertain = false;
    const currency = (value) =>
        "PKR " +
        value.toLocaleString("en-PK", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    const escape = (value) =>
        String(value).replace(
            /[&<>"']/g,
            (char) =>
                ({
                    "&": "&amp;",
                    "<": "&lt;",
                    ">": "&gt;",
                    '"': "&quot;",
                    "'": "&#39;",
                })[char],
        );
    const number = (id) => Number.parseFloat($(id).value) || 0;
    function totals() {
        const subtotal = cart.reduce(
            (sum, item) => sum + item.price * item.quantity,
            0,
        );
        return {
            subtotal,
            discount: number("discount"),
            total: Math.max(0, subtotal - number("discount")),
        };
    }
    function filterMenu() {
        const query = $("menu-search").value.trim().toLocaleLowerCase();
        let visible = 0;
        cards.forEach((card) => {
            const matches =
                (category === "all" || card.dataset.category === category) &&
                (card.dataset.name + " " + card.dataset.categoryName)
                    .toLocaleLowerCase()
                    .includes(query);
            card.hidden = !matches;
            if (matches) visible++;
        });
        $("menu-empty").hidden = visible > 0;
    }
    $("menu-search").addEventListener("input", filterMenu);
    document.querySelectorAll(".cat-pill").forEach((button) =>
        button.addEventListener("click", () => {
            category = button.dataset.category;
            document.querySelectorAll(".cat-pill").forEach((pill) => {
                const active = pill === button;
                pill.classList.toggle("active", active);
                pill.setAttribute("aria-pressed", String(active));
            });
            filterMenu();
        }),
    );
    cards.forEach((card) =>
        card.addEventListener("click", () => {
            if (submitting || checkoutUncertain || checkoutComplete) return;
            const existing = cart.find((item) => item.id === card.dataset.id);
            if (existing) existing.quantity++;
            else
                cart.push({
                    id: card.dataset.id,
                    name: card.dataset.name,
                    price: Number(card.dataset.price),
                    quantity: 1,
                    instructions: "",
                });
            $("checkout-feedback").textContent =
                card.dataset.name + " added to order.";
            renderCart();
        }),
    );
    function renderTotals() {
        const { subtotal, total } = totals();
        $("subtotal").textContent = currency(subtotal);
        $("total").textContent = currency(total);
        $("mobile-cart-total").textContent = currency(total);
        const cash = $("payment-method").value === "cash";
        const received = number("cash-received");
        $("cash-input-wrap").hidden = !cash;
        $("cash-received").required = cash;
        $("cash-received").disabled = !cash || submitting;
        $("cash-received").min = total.toFixed(2);
        $("change-wrap").classList.toggle(
            "d-none",
            !cash || !cart.length || received < total,
        );
        $("change-val").textContent = currency(Math.max(0, received - total));
        $("discount").max = subtotal.toFixed(2);
        $("process-order").disabled =
            !cart.length || submitting || checkoutUncertain || checkoutComplete;
    }
    function renderCart() {
        const inputs = document.createDocumentFragment();
        cart.forEach((item, index) => {
            for (const [name, value] of Object.entries({
                id: item.id,
                quantity: item.quantity,
                instructions: item.instructions,
            })) {
                const input = document.createElement("input");
                input.type = "hidden";
                input.name = `items[${index}][${name}]`;
                input.value = value;
                inputs.appendChild(input);
            }
        });
        $("checkout-items").replaceChildren(inputs);
        const count = cart.reduce((sum, item) => sum + item.quantity, 0);
        $("cart-count").textContent = count;
        $("order-item-count").textContent = count
            ? count +
              (count === 1 ? " item in this order" : " items in this order")
            : "No items added";
        $("clear-order").disabled =
            !cart.length || submitting || checkoutUncertain || checkoutComplete;
        if (!cart.length)
            $("cart-content").innerHTML =
                '<div class="empty-state"><div class="ticket-mark" aria-hidden="true">＋</div><h5>Nothing on the ticket.</h5><p>Tap something good to start.</p></div>';
        else
            $("cart-content").innerHTML = cart
                .map(
                    (item, index) =>
                        `<div class="cart-item-row"><div class="cart-item-header"><span class="fw-bold">${escape(item.name)}</span><span class="fw-bold">${currency(item.price * item.quantity)}</span></div><div class="cart-item-controls"><div class="qty-control"><button type="button" class="qty-btn" data-action="decrease" data-index="${index}" aria-label="Decrease ${escape(item.name)} quantity"><i class="fas fa-minus" aria-hidden="true"></i></button><span>${item.quantity}</span><button type="button" class="qty-btn" data-action="increase" data-index="${index}" aria-label="Increase ${escape(item.name)} quantity"><i class="fas fa-plus" aria-hidden="true"></i></button></div><div><button type="button" class="qty-btn" data-action="note" data-index="${index}" aria-label="Instructions for ${escape(item.name)}"><i class="far fa-edit" aria-hidden="true"></i></button><button type="button" class="qty-btn text-danger" data-action="remove" data-index="${index}" aria-label="Remove ${escape(item.name)}"><i class="far fa-trash-alt" aria-hidden="true"></i></button></div></div>${item.instructions ? `<div class="small text-muted mt-2">${escape(item.instructions)}</div>` : ""}</div>`,
                )
                .join("");
        renderTotals();
    }
    $("cart-content").addEventListener("click", (event) => {
        const button = event.target.closest("[data-action]");
        if (!button || submitting || checkoutUncertain || checkoutComplete)
            return;
        const index = Number(button.dataset.index);
        const item = cart[index];
        if (!item) return;
        if (button.dataset.action === "note") {
            editingItem = item;
            $("item-instructions").value = item.instructions;
            bootstrap.Modal.getOrCreateInstance($("noteModal")).show();
            return;
        }
        if (button.dataset.action === "increase") item.quantity++;
        if (button.dataset.action === "decrease") item.quantity--;
        if (button.dataset.action === "remove" || item.quantity <= 0)
            cart.splice(index, 1);
        renderCart();
        const next = $("cart-content").querySelector(
            `[data-index="${Math.min(index, cart.length - 1)}"][data-action="${button.dataset.action}"]`,
        );
        if (next) next.focus();
    });
    $("noteModal").addEventListener("shown.bs.modal", () =>
        $("item-instructions").focus(),
    );
    $("confirm-instructions").addEventListener("click", () => {
        if (editingItem && cart.includes(editingItem))
            editingItem.instructions = $("item-instructions").value.trim();
        bootstrap.Modal.getInstance($("noteModal")).hide();
        renderCart();
    });
    $("discount").addEventListener("input", renderTotals);
    $("cash-received").addEventListener("input", renderTotals);
    $("payment-method").addEventListener("change", renderTotals);
    $("clear-order").addEventListener("click", async () => {
        const result = await Swal.fire({
            title: "Clear this order?",
            text: "All items and payment details will be removed.",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Clear order",
            confirmButtonColor: "#24231f",
            cancelButtonText: "Keep order",
        });
        if (result.isConfirmed) resetOrder();
    });
    function resetOrder() {
        cart.length = 0;
        $("discount").value = "0";
        $("cash-received").value = "";
        $("payment-method").value = "cash";
        renderCart();
    }
    const mobileCart = matchMedia("(max-width:700px)");
    function syncCartAccess() {
        const open = $("cart-sidebar").classList.contains("open");
        $("cart-sidebar").inert = mobileCart.matches && !open;
        if (mobileCart.matches && open) {
            $("cart-sidebar").setAttribute("role", "dialog");
            $("cart-sidebar").setAttribute("aria-modal", "true");
        } else {
            $("cart-sidebar").removeAttribute("role");
            $("cart-sidebar").removeAttribute("aria-modal");
        }
    }
    listenMedia(mobileCart, () => {
        if (!mobileCart.matches) {
            $("cart-sidebar").classList.remove("open");
            $("cart-backdrop").hidden = true;
            $("open-cart").setAttribute("aria-expanded", "false");
        }
        syncCartAccess();
    });
    syncCartAccess();
    function showCart(open) {
        $("cart-sidebar").classList.toggle("open", open);
        $("cart-backdrop").hidden = !open;
        $("open-cart").setAttribute("aria-expanded", String(open));
        syncCartAccess();
        if (open) $("close-cart").focus();
        else $("open-cart").focus();
    }
    $("open-cart").addEventListener("click", () => showCart(true));
    $("close-cart").addEventListener("click", () => showCart(false));
    $("cart-backdrop").addEventListener("click", () => showCart(false));
    document.addEventListener("keydown", (event) => {
        const typing =
            /INPUT|TEXTAREA|SELECT/.test(event.target.tagName) ||
            event.target.isContentEditable;
        if (event.key === "/" && !typing) {
            event.preventDefault();
            $("menu-search").focus();
        }
        if (
            event.key === "Escape" &&
            !$("noteModal").classList.contains("show") &&
            $("cart-sidebar").classList.contains("open")
        )
            showCart(false);
        if (
            event.key === "Tab" &&
            $("cart-sidebar").classList.contains("open") &&
            matchMedia("(max-width:700px)").matches &&
            !$("noteModal").classList.contains("show")
        ) {
            const controls = [
                ...$("cart-sidebar").querySelectorAll(
                    "button:not(:disabled),input,select,a[href]",
                ),
            ].filter(
                (element) => element.getClientRects().length && !element.hidden,
            );
            const first = controls[0],
                last = controls[controls.length - 1];
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }
    });
    $("exact-cash").addEventListener("click", () => {
        $("cash-received").value = totals().total.toFixed(2);
        $("checkout-feedback").textContent = "";
        renderTotals();
    });
    form.addEventListener("submit", async (event) => {
        event.preventDefault();
        if (!cart.length || submitting || checkoutUncertain || checkoutComplete)
            return;
        const { subtotal, discount, total } = totals();
        const cash = $("payment-method").value === "cash";
        if (
            !$("discount").checkValidity() ||
            discount < 0 ||
            discount > subtotal
        ) {
            document.querySelector(".discount-details").open = true;
            showFeedback(
                "Enter a valid discount that does not exceed the subtotal.",
            );
            $("discount").reportValidity();
            return;
        }
        if (
            cash &&
            (!$("cash-received").checkValidity() ||
                number("cash-received") < total)
        ) {
            showFeedback(
                "Enter cash received or choose Exact to cover the total due.",
            );
            $("cash-received").focus();
            return;
        }
        submitting = true;
        $("process-order").innerHTML =
            '<span>Saving order…</span><i class="fas fa-spinner fa-spin" aria-hidden="true"></i>';
        $("checkout-feedback").textContent = "";
        $("clear-order").disabled = true;
        renderTotals();
        const fields = [$("discount"), $("cash-received"), $("payment-method")];
        fields.forEach((field) => (field.disabled = true));
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 45000);
        try {
            const response = await fetch(config.storeUrl, {
                signal: controller.signal,
                method: "POST",
                credentials: "same-origin",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]',
                    ).content,
                },
                body: JSON.stringify({
                    items: cart.map((item) => ({
                        id: item.id,
                        quantity: item.quantity,
                        instructions: item.instructions,
                    })),
                    discount,
                    payment_method: $("payment-method").value,
                    cash_received: cash ? number("cash-received") : null,
                }),
            });
            let result;
            try {
                result = await response.json();
            } catch (_) {
                checkoutUncertain = true;
                throw new Error("The order status could not be confirmed.");
            }
            if (!response.ok || !result.success || !result.order?.id) {
                checkoutUncertain =
                    (response.status >= 500 || response.ok) &&
                    result.order_saved !== false;
                const message =
                    response.status === 422
                        ? Object.values(result.errors || {}).flat()[0]
                        : response.status === 419
                          ? "Your session expired. Sign in again before placing this order."
                          : result.order_saved === false
                            ? "The order was not saved. Your items are still here; please try again."
                            : "The order could not be completed. Please check your session.";
                const reference = result.error_code
                    ? " (reference: " + result.error_code + ")"
                    : "";
                throw new Error(
                    (message || "Please check the order details.") + reference,
                );
            }
            checkoutComplete = true;
            window.location.assign(
                config.historyUrl +
                    "?order=" +
                    encodeURIComponent(result.order.id),
            );
            return;
        } catch (error) {
            if (
                !(error instanceof Error) ||
                error.name === "TypeError" ||
                error.name === "AbortError"
            )
                checkoutUncertain = true;
            if (checkoutUncertain) {
                $("checkout-feedback").innerHTML =
                    `Could not confirm the result. <a href="${escape(config.historyUrl)}">Check order history</a> before placing it again.`;
            } else {
                showFeedback(error.message);
            }
            $("checkout-feedback").scrollIntoView({ block: "nearest" });
        } finally {
            clearTimeout(timeout);
            submitting = false;
            fields.forEach((field) => (field.disabled = false));
            $("process-order").innerHTML =
                '<span>Place order</span><i class="fas fa-arrow-right" aria-hidden="true"></i>';
            renderTotals();
            $("clear-order").disabled =
                !cart.length || checkoutUncertain || checkoutComplete;
        }
    });
    filterMenu();
    renderCart();
})();
