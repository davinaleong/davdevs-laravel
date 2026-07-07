import { Children, cloneElement, isValidElement } from 'react';

function getRowStyle(styles, isEven) {
  const style = { borderBottom: '1px solid var(--border-default)' };
  if (styles.includes('striped') && isEven) {
    style.background = 'var(--bg-surface-1)';
  }
  return style;
}

function Table({ children, className = '', styles = [], caption, data, columns }) {
  const tableStyle = { width: '100%', background: 'var(--bg-page)' };
  if (styles.includes('bordered')) {
    tableStyle.border = '1px solid var(--border-default)';
  }

  if (data && columns) {
    return (
      <div className={`overflow-x-auto ${className}`}>
        <table style={tableStyle}>
          {caption && (
            <caption className="text-sm uppercase font-medium mb-3 text-left" style={{ color: 'var(--text-secondary)' }}>
              {caption}
            </caption>
          )}
          <thead>
            <tr style={{ ...getRowStyle(styles, false), fontWeight: 500 }}>
              {columns.map((column) => (
                <TableCell key={column.key} as="th" tableStyles={styles}>
                  {column.label}
                </TableCell>
              ))}
            </tr>
          </thead>
          <tbody>
            {data.map((row, index) => (
              <tr key={index} style={getRowStyle(styles, index % 2 === 0)}>
                {columns.map((column) => (
                  <TableCell key={column.key} tableStyles={styles}>
                    {column.render ? column.render(row[column.key], row) : row[column.key]}
                  </TableCell>
                ))}
              </tr>
            ))}
          </tbody>
          {Children.map(children, (child) => {
            if (isValidElement(child) && child.type === TableFoot) {
              return cloneElement(child, { tableStyles: styles });
            }
            return null;
          })}
        </table>
      </div>
    );
  }

  return (
    <div className={`overflow-x-auto ${className}`}>
      <table style={tableStyle}>
        {caption && (
          <caption className="text-sm uppercase font-medium mb-3 text-left" style={{ color: 'var(--text-secondary)' }}>
            {caption}
          </caption>
        )}
        {children}
      </table>
    </div>
  );
}

function TableFoot({ children, className = '', tableStyles = [] }) {
  return (
    <tfoot className={className}>
      {Children.map(children, (child) => (isValidElement(child) ? cloneElement(child, { tableStyles }) : child))}
    </tfoot>
  );
}

function TableRow({ children, className = '', tableStyles = [] }) {
  return (
    <tr className={className} style={getRowStyle(tableStyles, false)}>
      {Children.map(children, (child) => (isValidElement(child) ? cloneElement(child, { tableStyles }) : child))}
    </tr>
  );
}

function TableCell({ children, className = '', as: Component = 'td', colSpan, rowSpan, tableStyles = [] }) {
  const style = {
    padding: '0.5rem 1rem',
    textAlign: 'left',
    color: Component === 'th' ? 'var(--text-primary)' : 'var(--text-secondary)',
    fontWeight: Component === 'th' ? 600 : 400,
    borderRight: tableStyles.includes('bordered') ? '1px solid var(--border-default)' : undefined,
  };

  return (
    <Component className={className} colSpan={colSpan} rowSpan={rowSpan} style={style}>
      {children}
    </Component>
  );
}

const TableComponent = Object.assign(Table, {
  Foot: TableFoot,
  Row: TableRow,
  Cell: TableCell,
});

export default TableComponent;
