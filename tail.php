			</article>
		</div>
<script>
        // Pass PHP data to JavaScript
        const unitsMap = <?php echo json_encode($items); ?>;
        
        function updateUnits() {
            const itemSelect = document.getElementById('BR');
            const unitsSelect = document.getElementById('GR');
            const selectedItem = itemSelect.value;
            
            // Clear current options
            unitsSelect.innerHTML = '';
            
            if (selectedItem && unitsMap[selectedItem]) {
                // Enable the units dropdown
                unitsSelect.disabled = false;
                
                // Add default option
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = 'Select group number';
                unitsSelect.appendChild(defaultOption);
                
                // Add unit options from the file data
                unitsMap[selectedItem].forEach(unit => {
                    const option = document.createElement('option');
                    option.value = unit;
                    option.textContent = unit;
                    unitsSelect.appendChild(option);
                });
            } else {
                // Disable and reset if no item selected
                unitsSelect.disabled = true;
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = 'Select branch first';
                unitsSelect.appendChild(defaultOption);
            }
        }
</script>
	</body>

</html>


