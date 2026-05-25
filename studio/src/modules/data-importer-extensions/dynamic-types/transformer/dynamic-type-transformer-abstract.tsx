import type React from 'react'
import { injectable } from '@pimcore/studio-ui-bundle/app'

export type TransformerGroup = 'dataManipulation' | 'dataTypes' | 'loadImport'

@injectable()
export abstract class DynamicTypeTransformerAbstract {
    abstract readonly id: string
    abstract readonly label: string
    abstract readonly group: TransformerGroup

    abstract renderSettings(
        settings: Record<string, any>,
        onChange: (settings: Record<string, any>) => void
    ): React.JSX.Element | null
}
