@if (filament()->hasUnsavedChangesAlerts())
    @script
        <script>
            window.addEventListener('beforeunload', (event) => {
                if (typeof @this === 'undefined') {
                    return
                }

                if (
                    [
                        ...(@js($this instanceof HasActions) ? ($wire.mountedActions ?? []) : []),
                        ...(@js($this instanceof HasForms)
                            ? ($wire.mountedFormComponentActions ?? [])
                            : []),
                        ...(@js($this instanceof HasInfolists)
                            ? ($wire.mountedInfolistActions ?? [])
                            : []),
                        ...(@js($this instanceof HasTable)
                            ? [
                                  ...($wire.mountedTableActions ?? []),
                                  ...($wire.mountedTableBulkAction
                                      ? [$wire.mountedTableBulkAction]
                                      : []),
                              ]
                            : []),
                    ].length &&
                    !$wire?.__instance?.effects?.redirect
                ) {
                    event.preventDefault()
                    event.returnValue = true

                    return
                }
            })
        </script>
    @endscript
@endif
